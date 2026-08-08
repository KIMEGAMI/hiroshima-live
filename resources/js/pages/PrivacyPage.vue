<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <RouterLink
                to="/"
                class="text-sm font-bold text-red-400 hover:text-red-300"
            >
                hiroshima-live
            </RouterLink>

            <section class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-8">
                <p class="text-sm font-bold text-red-400">PRIVACY</p>

                <h1 class="mt-3 text-3xl font-black">
                    プライバシーポリシー
                </h1>

                <div class="mt-8 space-y-7 text-sm leading-7 text-zinc-300">
                    <section>
                        <h2 class="text-lg font-bold text-white">1. 取得する情報</h2>
                        <p class="mt-3">
                            当サービスは、アカウント登録時の名前、メールアドレス、ログイン認証に必要な情報、投稿されたライブ情報、画像、タグ、説明文を取得します。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">2. 利用目的</h2>
                        <p class="mt-3">
                            取得した情報は、サービス提供、本人確認、投稿管理、検索・カレンダー表示、お問い合わせ対応、不正利用防止、障害調査、機能改善のために利用します。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">3. パスワードと認証情報</h2>
                        <p class="mt-3">
                            パスワードはハッシュ化して保存します。パスワード、APIキー、秘密情報などを投稿本文やお問い合わせ内容に入力しないでください。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">4. 第三者提供</h2>
                        <p class="mt-3">
                            法令に基づく場合、本人の同意がある場合、またはサービス提供に必要な委託先に限定して取り扱う場合を除き、個人情報を第三者に提供しません。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">5. 安全管理</h2>
                        <p class="mt-3">
                            当サービスは、不正アクセス、漏えい、改ざん、紛失を防ぐため、必要かつ適切な安全管理措置を講じます。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">6. データ削除</h2>
                        <p class="mt-3">
                            アカウント削除時には、関連する登録情報の削除を行います。ただし、法令対応、不正利用調査、障害調査などのために必要な情報は、一定期間保持する場合があります。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">7. お問い合わせ</h2>
                        <p class="mt-3">
                            個人情報の確認、修正、削除、利用停止に関するお問い合わせは、サービス運営者までご連絡ください。
                        </p>
                    </section>
                </div>
            </section>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { RouterLink } from "vue-router";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";
</script>
