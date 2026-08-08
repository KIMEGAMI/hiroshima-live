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
                <p class="text-sm font-bold text-red-400">TERMS</p>

                <h1 class="mt-3 text-3xl font-black">利用規約</h1>

                <div class="mt-8 space-y-7 text-sm leading-7 text-zinc-300">
                    <section>
                        <h2 class="text-lg font-bold text-white">第1条 適用</h2>
                        <p class="mt-3">
                            本規約は、hiroshima-liveが提供する広島のライブ情報掲示板サービスの利用条件を定めるものです。利用者は、本サービスを利用することで、本規約に同意したものとみなされます。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">第2条 アカウント管理</h2>
                        <p class="mt-3">
                            利用者は、登録するメールアドレス、パスワード、ログイン情報を正確かつ安全に管理するものとします。アカウントを第三者に貸与、譲渡、共有することはできません。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">第3条 投稿情報の管理</h2>
                        <p class="mt-3">
                            利用者は、投稿するライブ情報、画像、タグ、説明文が正確であり、第三者の権利を侵害しないよう管理するものとします。公演内容、開催日、料金、会場情報などは、実際の主催者・会場・出演者の発表を必ず確認してください。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">第4条 禁止事項</h2>
                        <ul class="mt-3 list-disc space-y-2 pl-5">
                            <li>法令または公序良俗に反する行為</li>
                            <li>第三者の著作権、肖像権、プライバシー、信用を侵害する行為</li>
                            <li>虚偽情報、なりすまし、迷惑行為、スパム投稿</li>
                            <li>不正アクセス、脆弱性の悪用、過度な負荷をかける行為</li>
                            <li>本サービスの運営を妨害する行為</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">第5条 サービスの変更・停止</h2>
                        <p class="mt-3">
                            当サービスは、保守、障害対応、機能改善、法令対応などのため、サービス内容を変更または一時停止することがあります。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">第6条 免責</h2>
                        <p class="mt-3">
                            当サービスは、投稿情報の完全性、正確性、最新性、ライブ開催、チケット購入可否、出演者変更の有無を保証するものではありません。利用により発生した損害について、法令上認められる範囲で責任を負わないものとします。
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-bold text-white">第7条 規約の変更</h2>
                        <p class="mt-3">
                            本規約を変更する場合、サービス上での表示その他適切な方法により通知します。
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
