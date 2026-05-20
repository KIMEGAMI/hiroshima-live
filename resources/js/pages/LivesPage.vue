<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-red-400">LIVE EVENTS</p>

                    <h1 class="mt-2 text-4xl font-black">
                        {{ pageTitle }}
                    </h1>
                </div>

                <RouterLink
                    to="/lives/create"
                    class="rounded-full bg-red-500 px-5 py-3 text-sm font-bold hover:bg-red-600"
                >
                    ライブ情報を投稿
                </RouterLink>
            </div>

            <div
                v-if="selectedDate"
                class="mb-8 flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4"
            >
                <p class="text-zinc-300">
                    {{ selectedDateLabel }} のライブを表示中
                </p>

                <RouterLink
                    to="/lives"
                    class="text-sm font-bold text-red-400 hover:text-red-300"
                >
                    絞り込み解除
                </RouterLink>
            </div>

            <div
                v-if="lives.length > 0"
                class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <LiveCard v-for="live in lives" :key="live.id" :live="live" />
            </div>

            <div
                v-else
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                ライブ情報はまだありません。
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { useRoute } from "vue-router";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";
import LiveCard from "../components/lives/LiveCard.vue";

const route = useRoute();

const lives = ref([]);

const selectedDate = computed(() => {
    return route.query.date || "";
});

const selectedDateLabel = computed(() => {
    if (!selectedDate.value) {
        return "";
    }

    return String(selectedDate.value).replaceAll("-", "/");
});

const pageTitle = computed(() => {
    if (selectedDate.value) {
        return `${selectedDateLabel.value} のライブ`;
    }

    return "ライブ一覧";
});

const fetchLives = async () => {
    const query = selectedDate.value
        ? `?date=${encodeURIComponent(selectedDate.value)}`
        : "";

    const response = await fetch(`/api/lives${query}`);
    lives.value = await response.json();
};

watch(
    () => route.query.date,
    () => {
        fetchLives();
    },
);

onMounted(() => {
    fetchLives();
});
</script>
