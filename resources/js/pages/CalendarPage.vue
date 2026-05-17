<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <p class="text-sm font-bold text-red-400">CALENDAR</p>
            <h1 class="mt-2 text-4xl font-black">ライブカレンダー</h1>

            <div class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">2026年6月</h2>
                    <p class="text-sm text-zinc-400">
                        日付クリックで詳細表示予定
                    </p>
                </div>

                <div class="grid grid-cols-7 gap-3 text-center">
                    <div
                        v-for="day in days"
                        :key="day"
                        class="text-sm text-zinc-500"
                    >
                        {{ day }}
                    </div>

                    <div
                        v-for="date in 30"
                        :key="date"
                        class="min-h-24 rounded-2xl bg-zinc-900 p-3 text-left"
                        :class="{
                            'border border-red-500 bg-red-500/10':
                                eventDays.includes(date),
                        }"
                    >
                        <p class="font-bold">{{ date }}</p>
                        <p
                            v-if="eventDays.includes(date)"
                            class="mt-2 rounded-full bg-red-500 px-2 py-1 text-xs font-bold"
                        >
                            LIVE
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const days = ["日", "月", "火", "水", "木", "金", "土"];
const eventDays = [5, 12, 15, 21, 28];
</script>
