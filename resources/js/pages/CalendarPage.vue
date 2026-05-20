<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <p class="text-sm font-bold text-red-400">CALENDAR</p>
            <h1 class="mt-2 text-4xl font-black">ライブカレンダー</h1>

            <div class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-4 py-2 font-bold hover:bg-white/10"
                        @click="moveMonth(-1)"
                    >
                        ← 前の月
                    </button>

                    <div class="text-center">
                        <h2 class="text-2xl font-bold">
                            {{ currentYear }}年{{ currentMonth }}月
                        </h2>

                        <button
                            type="button"
                            class="mt-3 rounded-full border border-white/20 px-4 py-2 text-sm font-bold hover:bg-white/10"
                            @click="backToThisMonth"
                        >
                            今月へ戻る
                        </button>
                    </div>

                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-4 py-2 font-bold hover:bg-white/10"
                        @click="moveMonth(1)"
                    >
                        次の月 →
                    </button>
                </div>

                <p class="mb-6 text-sm text-zinc-400">
                    LIVE表示の日付にライブ情報があります
                </p>

                <div class="grid grid-cols-7 gap-3 text-center">
                    <div
                        v-for="day in days"
                        :key="day"
                        class="text-sm text-zinc-500"
                    >
                        {{ day }}
                    </div>

                    <div
                        v-for="blank in firstDayOfMonth"
                        :key="'blank-' + blank"
                    ></div>

                    <RouterLink
                        v-for="date in daysInMonth"
                        :key="date"
                        :to="
                            hasEvent(date)
                                ? `/lives?date=${formatDate(date)}`
                                : '/calendar'
                        "
                        class="min-h-24 rounded-2xl bg-zinc-900 p-3 text-left transition hover:bg-zinc-800"
                        :class="{
                            'border border-red-500 bg-red-500/10':
                                hasEvent(date),
                        }"
                    >
                        <p class="font-bold">{{ date }}</p>

                        <p
                            v-if="hasEvent(date)"
                            class="mt-2 inline-block rounded-full bg-red-500 px-2 py-1 text-xs font-bold"
                        >
                            LIVE
                        </p>
                    </RouterLink>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const today = new Date();

const displayYear = ref(today.getFullYear());
const displayMonthIndex = ref(today.getMonth());

const days = ["日", "月", "火", "水", "木", "金", "土"];

const lives = ref([]);

const currentYear = computed(() => displayYear.value);

const currentMonth = computed(() => displayMonthIndex.value + 1);

const currentYearMonth = computed(() => {
    return `${displayYear.value}-${String(displayMonthIndex.value + 1).padStart(2, "0")}`;
});

const daysInMonth = computed(() => {
    return new Date(
        displayYear.value,
        displayMonthIndex.value + 1,
        0,
    ).getDate();
});

const firstDayOfMonth = computed(() => {
    return new Date(displayYear.value, displayMonthIndex.value, 1).getDay();
});

const eventDays = computed(() => {
    return lives.value
        .filter((live) => live.event_date?.startsWith(currentYearMonth.value))
        .map((live) => Number(live.event_date?.slice(8, 10)))
        .filter((day) => !Number.isNaN(day));
});

const formatDate = (date) => {
    return `${currentYearMonth.value}-${String(date).padStart(2, "0")}`;
};

const hasEvent = (date) => {
    return eventDays.value.includes(date);
};

const moveMonth = (amount) => {
    const nextMonth = new Date(
        displayYear.value,
        displayMonthIndex.value + amount,
        1,
    );

    displayYear.value = nextMonth.getFullYear();
    displayMonthIndex.value = nextMonth.getMonth();
};

const backToThisMonth = () => {
    displayYear.value = today.getFullYear();
    displayMonthIndex.value = today.getMonth();
};

const fetchLives = async () => {
    const response = await fetch("/api/lives");
    lives.value = await response.json();
};

onMounted(() => {
    fetchLives();
});
</script>
