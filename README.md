# Hiroshima Live

広島のライブイベント情報を投稿・共有できるライブ情報共有サービスです。

Laravel + Vue 3 + SPA構成で開発しています。

---

# 概要

ライブハウス・バンドイベント情報をユーザーが投稿できるWebサービスです。

ライブイベントの一覧表示、詳細表示、画像投稿、認証機能などを実装しています。

「広島のライブ情報をもっと探しやすくしたい」という目的で制作しました。

---

# 使用技術

## Backend

- PHP 8.4
- Laravel 13
- Sanctum
- MySQL / MariaDB

## Frontend

- Vue 3
- Vue Router
- Vite
- Tailwind CSS
- Axios

## Infrastructure

- Ubuntu
- Apache
- GitHub
- Let's Encrypt
- MariaDB

---

# 主な機能

- ユーザー登録
- ログイン / ログアウト
- ライブ投稿
- ライブ一覧表示
- ライブ詳細表示
- 画像アップロード
- カレンダー表示
- レスポンシブ対応
- SPA構成
- API通信

---

# システム構成

- Laravel API
- Vue 3 SPA
- Sanctum認証
- Axios通信

フロントエンドとバックエンドを分離した構成で開発しています。

---

# 苦労した点

## Sanctum認証まわり

特に苦労したのはログイン認証部分です。

Vue SPA + Laravel Sanctum の組み合わせでは、

- セッション維持
- CSRF
- Cookie
- stateful domain
- CORS

などの理解が必要でした。

本番環境では、

- localhost では動くが本番でログインできない
- ログイン後に画面遷移しない
- APIだけ401になる
- セッションが維持されない

など、多くの問題が発生しました。

最終的に、

- SANCTUM_STATEFUL_DOMAINS
- SESSION_DOMAIN
- Axios の withCredentials
- Apache 側設定
- HTTPS対応

を見直すことで解決しました。

---

## SPA構成

Vue Router を利用したSPA構成で、

- ページリロード時の404
- Viteとの連携
- Laravel側ルーティングとの競合

にも苦労しました。

特に本番環境では Apache Rewrite の設定が必要で、
SPA特有の問題を多く経験しました。

---

## カレンダー機能

ライブ一覧とカレンダー表示を連携する際に、

- 月ズレ
- Dateオブジェクト
- タイムゾーン
- Vue側描画

で問題が発生しました。

特に「翌月が表示される問題」の原因調査では、
Vue側・JavaScript側・APIレスポンス側を切り分けながらデバッグしました。

---

# 工夫した点

- SPAによる高速な画面遷移
- TailwindによるモダンUI
- レスポンシブ対応
- 画像アップロード対応
- 本番環境へのデプロイ
- HTTPS対応
- GitHub運用

---

# 今後追加予定

- Googleログイン
- お気に入り機能
- ライブ検索
- タグ機能
- ライブハウス別一覧
- コメント機能
- 通知機能

---

# 学んだこと

この制作を通して、

- Laravel API設計
- Vue SPA構築
- 認証
- Linuxサーバ構築
- Apache設定
- HTTPS化
- Git/GitHub運用

を実践的に学ぶことができました。

特に「本番環境で動かす難しさ」を強く経験できたことが大きな学びでした。

---
