import AppHeader from '../components/layout/AppHeader.vue'; import AppFooter
from '../components/layout/AppFooter.vue'; import LiveCard from
'../components/lives/LiveCard.vue';

<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main>
            <section
                class="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-2 md:items-center"
            >
                <div>
                    <p class="mb-4 text-sm font-bold text-red-400">
                        HIROSHIMA LIVE INFORMATION
                    </p>

                    <h1 class="text-5xl font-black leading-tight md:text-6xl">
                        広島のライブ情報を<br />
                        もっと探しやすく。
                    </h1>

                    <p class="mt-6 text-lg leading-8 text-zinc-300">
                        ライブハウス、アーティスト、イベント情報をまとめて探せるライブ情報掲示板を準備中です。
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a
                            href="/lives"
                            class="rounded-full bg-red-500 px-6 py-3 font-bold text-white shadow-lg shadow-red-500/30 hover:bg-red-600"
                        >
                            新着ライブを見る
                        </a>

                        <a
                            href="/calendar"
                            class="rounded-full border border-white/20 px-6 py-3 font-bold text-white hover:bg-white/10"
                        >
                            カレンダーを見る
                        </a>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-white/10 bg-white/5 p-3 shadow-2xl"
                >
                    <img
                        :src="'/images/hiroshima.png'"
                        alt="広島ライブ"
                        class="rounded-2xl"
                    />
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-6 py-10">
                <div class="mb-6 flex items-end justify-between">
                    <div>
                        <p class="text-sm font-bold text-red-400">NEW EVENTS</p>
                        <h2 class="mt-2 text-3xl font-black">新着ライブ</h2>
                    </div>

                    <a
                        href="/lives"
                        class="text-sm font-bold text-zinc-300 hover:text-white"
                    >
                        もっと見る →
                    </a>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <LiveCard
                        v-for="live in lives"
                        :key="live.id"
                        :live="live"
                    />
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-6 py-12">
                <a
                    href="/calendar"
                    class="block rounded-3xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                >
                    <p class="text-sm font-bold text-red-400">CALENDAR</p>
                    <h2 class="mt-2 text-3xl font-black">ライブカレンダー</h2>

                    <div
                        class="mt-6 grid grid-cols-7 gap-2 text-center text-sm"
                    >
                        <div
                            v-for="day in days"
                            :key="day"
                            class="text-zinc-500"
                        >
                            {{ day }}
                        </div>

                        <div
                            v-for="date in 30"
                            :key="date"
                            class="rounded-xl bg-zinc-900 py-3 text-zinc-300"
                            :class="{
                                'bg-red-500 text-white font-bold':
                                    eventDays.includes(date),
                            }"
                        >
                            {{ date }}
                        </div>
                    </div>

                    <p class="mt-5 text-sm text-zinc-400">
                        日付をクリックしてライブ情報を確認できるようにする予定です。
                    </p>
                </a>
            </section>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import AppHeader from "./layout/AppHeader.vue";
import AppFooter from "./layout/AppFooter.vue";
import LiveCard from "./lives/LiveCard.vue";

const lives = [
    {
        id: 1,
        title: "Hiroshima Rock Night",
        date: "2026.06.15",
        house: "広島 CLUB QUATTRO",
        description:
            "広島中心部で開催されるロックイベント。地元バンドを中心に出演予定。",
        image: "/images/hiroshima.png",
    },
    {
        id: 2,
        title: "Indie Music Session",
        date: "2026.06.21",
        house: "Live Space Reed",
        description:
            "インディーズアーティストによるライブイベント。新しい音楽との出会いを楽しめます。",
        image: "/images/hiroshima.png",
    },
    {
        id: 3,
        title: "Acoustic Night",
        date: "2026.06.28",
        house: "広島市内ライブバー",
        description:
            "アコースティック中心の落ち着いたライブイベント。仕事帰りにもおすすめ。",
        image: "/images/hiroshima.png",
    },
];

const days = ["日", "月", "火", "水", "木", "金", "土"];
const eventDays = [5, 12, 15, 21, 28];
</script>
