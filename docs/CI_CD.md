# CI/CD 運用メモ

## 結論

GitHub Actionsで、Pull Requestと`main`へのpush時にCIを実行します。CDは`main`のCI成功後、または手動実行で本番サーバーへSSHデプロイします。

## CI

`.github/workflows/ci.yml` は次を実行します。

- PHP依存関係のインストール
- Node依存関係のインストール
- Laravelテスト（`tests/Unit` と `tests/Feature/Api`）
- Viteビルド

CIのテストDBはSQLiteのインメモリDBです。GitHub Actions上にMySQLやMariaDBの認証情報を置かないため、漏えいリスクを小さくできます。

ローカルで同じテストを実行する場合、PHP拡張の`pdo_sqlite`が必要です。

追加済みのAPIテストでは、ログイン成功/失敗、ログイン中ユーザー取得、未ログイン投稿拒否、ログイン後のライブ投稿作成、投稿バリデーション、本人投稿一覧を確認します。

既存の`tests/Feature/Auth`と`tests/Feature/ProfileTest.php`は、Laravel BreezeのWebルート前提が残っており、現在のSPA + API構成とズレています。現時点のCI対象からは外し、別タスクで現行画面/APIに合わせて整理します。

## CD

`.github/workflows/deploy.yml` は次を実行します。

- 本番用依存関係のインストール
- フロントエンドビルド
- リリースアーカイブ作成
- SSHで本番サーバーへアップロード
- `releases/{commit sha}` に展開
- `shared/.env` と `shared/storage` を共有
- マイグレーションとLaravel最適化
- `current` シンボリックリンクを新リリースへ切り替え

自動デプロイはCIが成功した場合のみ実行します。手動実行はGitHub Actionsの`workflow_dispatch`から行えます。

## GitHub Secrets

Repository Secrets、またはproduction Environment Secretsに次を設定します。

| Secret | 用途 |
| --- | --- |
| `DEPLOY_HOST` | デプロイ先ホスト名またはIPアドレス |
| `DEPLOY_USER` | SSH接続ユーザー |
| `DEPLOY_SSH_PRIVATE_KEY` | デプロイ専用秘密鍵 |
| `DEPLOY_SSH_KNOWN_HOSTS` | `ssh-keyscan -H {DEPLOY_HOST}` で取得したホスト鍵 |
| `DEPLOY_PATH` | サーバー上のデプロイ先ディレクトリ |

## サーバー側の前提

`DEPLOY_PATH` 配下に次を準備します。

```text
DEPLOY_PATH/
  shared/
    .env
    storage/
  releases/
  current -> releases/{commit sha}
```

ApacheのDocumentRootは次を向けます。

```text
DEPLOY_PATH/current/public
```

## セキュリティ方針

- 本番の`.env`はGitHubに置かず、サーバーの`shared/.env`で管理します。
- GitHub SecretsにはSSH接続に必要な値だけを入れます。
- デプロイ鍵はこのリポジトリ専用にします。
- SSHホスト鍵は`DEPLOY_SSH_KNOWN_HOSTS`で固定し、デプロイ時の自動取得はしません。
- `main`への直接pushは禁止し、Pull Request経由でCI通過後にマージします。
- GitHub Environmentのproductionには承認ルールを設定することを推奨します。

## 事実と未確定事項

事実:

- このリポジトリはLaravel + Vue/Vite構成です。
- 既存の`.github/workflows`はありませんでした。
- `.env`は`.gitignore`に含まれています。

未確定事項:

- 本番サーバーの実際のOSユーザー、パス、Apache設定は未確認です。
- 本番サーバーにPHP 8.4、Composer、必要なPHP拡張が入っている前提です。
- Queue WorkerやSchedulerを本番運用する場合、Supervisorやcronの設定が別途必要です。
- ロールバック手順はサーバー構成確定後に具体化できます。
