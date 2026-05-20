<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <section class="grid gap-10 md:grid-cols-2 md:items-center">
                <div>
                    <p class="text-sm font-bold text-red-400">
                        HIROSHIMA LIVE INFORMATION
                    </p>

                    <h1 class="mt-3 text-5xl font-black leading-tight">
                        広島のライブ情報を<br />
                        もっと探しやすく。
                    </h1>

                    <p class="mt-6 text-zinc-300">
                        広島のライブハウス、アーティスト、イベント情報をまとめて探せるライブ情報掲示板です。
                    </p>

                    <div class="mt-8 flex gap-4">
                        <RouterLink
                            to="/lives"
                            class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                        >
                            新着ライブを見る
                        </RouterLink>

                        <RouterLink
                            to="/calendar"
                            class="rounded-full border border-white/20 px-6 py-3 font-bold hover:bg-white/10"
                        >
                            カレンダーを見る
                        </RouterLink>
                    </div>
                </div>

                <img
                    :src="'/images/hiroshima.png'"
                    alt="広島ライブ"
                    class="rounded-3xl border border-white/10 shadow-2xl"
                />
            </section>

            <section class="mt-16 grid gap-8 lg:grid-cols-[1fr_360px]">
                <div>
                    <div class="mb-6 flex items-end justify-between">
                        <div>
                            <p class="text-sm font-bold text-red-400">
                                NEW EVENTS
                            </p>

                            <h2 class="mt-2 text-3xl font-black">
                                {{
                                    selectedDate
                                        ? selectedDateLabel + " のライブ"
                                        : "新着ライブ"
                                }}
                            </h2>
                        </div>

                        <RouterLink
                            to="/lives"
                            class="text-sm font-bold text-zinc-300 hover:text-white"
                        >
                            もっと見る →
                        </RouterLink>
                    </div>

                    <div
                        v-if="lives.length > 0"
                        class="grid gap-6 md:grid-cols-2"
                    >
                        <LiveCard
                            v-for="live in lives"
                            :key="live.id"
                            :live="live"
                        />
                    </div>

                    <div
                        v-else
                        class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
                    >
                        この日のライブ情報はまだありません。
                    </div>
                </div>

                <aside
                    class="rounded-3xl border border-white/10 bg-white/5 p-6"
                >
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-red-400">
                                MINI CALENDAR
                            </p>

                            <h2 class="mt-1 text-2xl font-black">
                                {{ currentYear }}年{{ currentMonth }}月
                            </h2>
                        </div>

                        <button
                            v-if="selectedDate"
                            class="text-sm text-zinc-400 hover:text-white"
                            @click="clearDate"
                        >
                            解除
                        </button>
                    </div>

                    <div class="grid grid-cols-7 gap-2 text-center text-sm">
                        <div
                            v-for="day in days"
                            :key="day"
                            class="py-2 text-zinc-500"
                        >
                            {{ day }}
                        </div>

                        <div
                            v-for="blank in firstDayOfMonth"
                            :key="'blank-' + blank"
                        ></div>

                        <button
                            v-for="date in daysInMonth"
                            :key="date"
                            class="rounded-xl py-3 text-zinc-300 transition hover:bg-white/10"
                            :class="{
                                'bg-red-500 text-white font-bold':
                                    selectedDay === date,
                                'border border-red-500/60':
                                    eventDays.includes(date) &&
                                    selectedDay !== date,
                            }"
                            @click="selectDate(date)"
                        >
                            {{ date }}
                        </button>
                    </div>

                    <p class="mt-5 text-sm leading-6 text-zinc-400">
                        赤枠の日付にはライブ情報があります。日付をクリックすると、その日のライブだけを表示します。
                    </p>

                    <RouterLink
                        to="/calendar"
                        class="mt-5 inline-block rounded-full border border-white/20 px-5 py-2 text-sm font-bold hover:bg-white/10"
                    >
                        大きいカレンダーを見る
                    </RouterLink>
                </aside>
            </section>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";
import LiveCard from "../components/lives/LiveCard.vue";

const today = new Date();

const currentYear = today.getFullYear();
const currentMonth = today.getMonth() + 1;
const currentMonthIndex = today.getMonth();

const currentYearMonth = computed(() => {
    return `${currentYear}-${String(currentMonth).padStart(2, "0")}`;
});

const daysInMonth = computed(() => {
    return new Date(currentYear, currentMonth, 0).getDate();
});

const firstDayOfMonth = computed(() => {
    return new Date(currentYear, currentMonthIndex, 1).getDay();
});

const lives = ref([]);
const allLives = ref([]);
const selectedDate = ref(null);

const days = ["日", "月", "火", "水", "木", "金", "土"];

const eventDays = computed(() => {
    return allLives.value
        .filter((live) => live.event_date?.startsWith(currentYearMonth.value))
        .map((live) => Number(live.event_date?.slice(8, 10)))
        .filter((day) => !Number.isNaN(day));
});

const selectedDay = computed(() => {
    if (!selectedDate.value) {
        return null;
    }

    return Number(selectedDate.value.slice(8, 10));
});

const selectedDateLabel = computed(() => {
    if (!selectedDate.value) {
        return "";
    }

    return selectedDate.value.replaceAll("-", "/");
});

const fetchLives = async () => {
    const response = await fetch("/api/lives");
    const data = await response.json();

    allLives.value = data;
    lives.value = data.slice(0, 3);
};

const selectDate = async (date) => {
    const day = String(date).padStart(2, "0");
    selectedDate.value = `${currentYearMonth.value}-${day}`;

    const response = await fetch(`/api/lives?date=${selectedDate.value}`);
    lives.value = await response.json();
};

const clearDate = () => {
    selectedDate.value = null;
    lives.value = allLives.value.slice(0, 3);
};

onMounted(() => {
    fetchLives();
});
</script>
