# 本番デプロイ前バックアップ・復旧手順

この手順は、Hiroshima Live の本番デプロイ前に人間が確認するためのチェックリストです。
このファイルだけでは本番バックアップの取得完了を証明できません。

## デプロイGO前の確認

- [ ] 本番の `shared/.env` がバックアップ対象から除外されている
- [ ] 本番DBの種類、接続先、バックアップ保存先、保存期間を確認した
- [ ] デプロイ直前のDBバックアップを取得し、復元可能性を確認した
- [ ] バックアップの保存先が本番VPSの同一ディスクだけになっていない
- [ ] 現在のリリースを `releases/{commit sha}` として特定した
- [ ] 今回のmigrationが後方互換で、旧リリースへ切り戻しても動作することを確認した
- [ ] GitHub ActionsのCI、Composer監査、ビルドが成功している

## DBバックアップ

実際のDB種別と認証方式を確認してから、VPS管理者が環境に合うコマンドを実行します。
パスワードをコマンドへ直接書かず、VPS上の権限制限された設定ファイルまたは環境変数を使います。

MySQL/MariaDBの例:

```bash
mysqldump --defaults-extra-file=/secure/path/backup.cnf \
  --single-transaction --routines --triggers DATABASE_NAME \
  | gzip > /secure/backup/path/hiroshima-live-YYYYMMDD-HHMMSS.sql.gz
```

取得後は、ファイルサイズ、作成日時、終了コード、保存先の可用性を確認します。
本番データを上書きする復元テストは、別の一時DBで実施してください。

## リリース切り戻し

1. GitHub Actionsのデプロイを停止し、現在の障害状況と時刻を記録する。
2. `current` が指しているリリースと、直前の正常リリースを確認する。
3. 直前の正常リリースへ `current` のシンボリックリンクを切り替える。
4. 切り戻し先で `php artisan optimize:clear` と `php artisan optimize` を実行する。
5. メンテナンスモードを解除する。
6. `/up`、トップページ、ログイン、投稿一覧、投稿作成を確認する。
7. migrationで互換性が壊れている場合は、アプリだけ戻さず、承認済みバックアップからDB復元を行う。

例:

```bash
ln -sfn "$DEPLOY_PATH/releases/PREVIOUS_RELEASE_SHA" "$DEPLOY_PATH/current"
cd "$DEPLOY_PATH/current"
php artisan optimize:clear
php artisan optimize
php artisan up
```

## 現時点の未確認事項

- 本番VPS上の実際の `DEPLOY_PATH`、DB名、DB種別、バックアップ保存先は未確認です。
- 本番DBバックアップの取得・復元成功は未確認です。
- 本番での切り戻し操作は未実施です。
- 以上が確認できるまで、本番デプロイGOを出してはいけません。
