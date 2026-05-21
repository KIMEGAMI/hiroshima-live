<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-red-400">LIVE EVENTS</p>

                    <h1 class="mt-2 text-4xl font-black">ライブ一覧</h1>

                    <p class="mt-4 text-zinc-400">
                        広島のライブ情報を新しい順に表示しています。
                    </p>
                </div>

                <RouterLink
                    to="/lives/create"
                    class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                >
                    ライブを投稿
                </RouterLink>
            </div>

            <div
                v-if="selectedDate"
                class="mb-6 rounded-2xl border border-red-500/40 bg-red-500/10 p-4"
            >
                <div class="flex items-center justify-between gap-4">
                    <p class="font-bold">
                        {{ selectedDateLabel }} のライブを表示中
                    </p>

                    <RouterLink
                        to="/lives"
                        class="text-sm font-bold text-red-300 hover:text-red-200"
                    >
                        全件表示に戻る
                    </RouterLink>
                </div>
            </div>

            <div
                v-if="isLoading"
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                読み込み中です。
            </div>

            <div
                v-else-if="errorMessage"
                class="rounded-3xl border border-red-500/40 bg-red-500/10 p-8 text-red-300"
            >
                {{ errorMessage }}
            </div>

            <div
                v-else-if="lives.length > 0"
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
const isLoading = ref(false);
const errorMessage = ref("");

const selectedDate = computed(() => {
    return route.query.date || "";
});

const selectedDateLabel = computed(() => {
    if (!selectedDate.value) {
        return "";
    }

    return selectedDate.value.replaceAll("-", "/");
});

const fetchLives = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        const query = selectedDate.value
            ? `?date=${encodeURIComponent(selectedDate.value)}`
            : "";

        const response = await fetch(`/api/lives${query}`, {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("ライブ情報の取得に失敗しました。");
        }

        lives.value = await response.json();
    } catch (error) {
        errorMessage.value = "ライブ情報の取得に失敗しました。";
        lives.value = [];
    } finally {
        isLoading.value = false;
    }
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
