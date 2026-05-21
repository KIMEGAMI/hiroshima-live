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

                    <div class="mt-8 flex flex-wrap gap-4">
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

                        <RouterLink
                            to="/lives/create"
                            class="rounded-full border border-red-500/50 px-6 py-3 font-bold text-red-300 hover:bg-red-500/10"
                        >
                            ライブを投稿
                        </RouterLink>
                    </div>
                </div>

                <img
                    src="/images/hiroshima.png"
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
                        v-if="isLoading"
                        class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
                    >
                        読み込み中です。
                    </div>

                    <div
                        v-else-if="lives.length > 0"
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
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <button
                            type="button"
                            class="rounded-full border border-white/20 px-3 py-1 font-bold hover:bg-white/10"
                            @click="moveMonth(-1)"
                        >
                            ←
                        </button>

                        <div class="text-center">
                            <p class="text-sm font-bold text-red-400">
                                MINI CALENDAR
                            </p>

                            <h2 class="mt-1 text-2xl font-black">
                                {{ currentYear }}年{{ currentMonth }}月
                            </h2>
                        </div>

                        <button
                            type="button"
                            class="rounded-full border border-white/20 px-3 py-1 font-bold hover:bg-white/10"
                            @click="moveMonth(1)"
                        >
                            →
                        </button>
                    </div>

                    <button
                        type="button"
                        class="mb-5 rounded-full border border-white/20 px-4 py-2 text-sm font-bold hover:bg-white/10"
                        @click="backToThisMonth"
                    >
                        今月へ戻る
                    </button>

                    <button
                        v-if="selectedDate"
                        type="button"
                        class="mb-5 ml-3 text-sm text-zinc-400 hover:text-white"
                        @click="clearDate"
                    >
                        選択解除
                    </button>

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
                            type="button"
                            class="rounded-xl py-3 text-zinc-300 transition hover:bg-white/10"
                            :class="{
                                'bg-red-500 font-bold text-white':
                                    selectedDate === formatDate(date),
                                'border border-red-500/60':
                                    eventDays.includes(date) &&
                                    selectedDate !== formatDate(date),
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

const displayYear = ref(today.getFullYear());
const displayMonthIndex = ref(today.getMonth());

const lives = ref([]);
const allLives = ref([]);
const selectedDate = ref(null);
const isLoading = ref(false);

const days = ["日", "月", "火", "水", "木", "金", "土"];

const currentYear = computed(() => displayYear.value);

const currentMonth = computed(() => {
    return displayMonthIndex.value + 1;
});

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
    return allLives.value
        .filter((live) => live.event_date?.startsWith(currentYearMonth.value))
        .map((live) => Number(live.event_date?.slice(8, 10)))
        .filter((day) => !Number.isNaN(day));
});

const selectedDateLabel = computed(() => {
    if (!selectedDate.value) {
        return "";
    }

    return selectedDate.value.replaceAll("-", "/");
});

const formatDate = (date) => {
    return `${currentYearMonth.value}-${String(date).padStart(2, "0")}`;
};

const fetchLives = async () => {
    isLoading.value = true;

    try {
        const response = await fetch("/api/lives", {
            headers: {
                Accept: "application/json",
            },
        });

        const data = await response.json();

        allLives.value = data;
        lives.value = data.slice(0, 4);
    } catch (error) {
        allLives.value = [];
        lives.value = [];
    } finally {
        isLoading.value = false;
    }
};

const selectDate = async (date) => {
    selectedDate.value = formatDate(date);
    isLoading.value = true;

    try {
        const response = await fetch(`/api/lives?date=${selectedDate.value}`, {
            headers: {
                Accept: "application/json",
            },
        });

        lives.value = await response.json();
    } catch (error) {
        lives.value = [];
    } finally {
        isLoading.value = false;
    }
};

const clearDate = () => {
    selectedDate.value = null;
    lives.value = allLives.value.slice(0, 4);
};

const moveMonth = (amount) => {
    const nextMonth = new Date(
        displayYear.value,
        displayMonthIndex.value + amount,
        1,
    );

    displayYear.value = nextMonth.getFullYear();
    displayMonthIndex.value = nextMonth.getMonth();

    clearDate();
};

const backToThisMonth = () => {
    displayYear.value = today.getFullYear();
    displayMonthIndex.value = today.getMonth();

    clearDate();
};

onMounted(() => {
    fetchLives();
});
</script>
