<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-5xl px-6 py-12">
            <RouterLink
                to="/lives"
                class="text-sm font-bold text-zinc-400 hover:text-white"
            >
                ← ライブ一覧へ戻る
            </RouterLink>

            <div
                v-if="isLoading"
                class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                読み込み中です...
            </div>

            <div
                v-else-if="errorMessage"
                class="mt-8 rounded-3xl border border-red-500/40 bg-red-500/10 p-8 text-red-300"
            >
                {{ errorMessage }}
            </div>

            <div
                v-else-if="live"
                class="mt-8 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl"
            >
                <div class="bg-zinc-900 p-6">
                    <img
                        :src="imageUrl"
                        :alt="live.title || 'ライブ画像'"
                        class="mx-auto aspect-[2/3] max-h-[720px] w-full max-w-md rounded-2xl object-cover"
                    />
                </div>

                <div class="p-8">
                    <p class="text-sm font-bold text-red-400">
                        {{ live.event_date || "日付未定" }}
                    </p>

                    <h1 class="mt-3 text-4xl font-black">
                        {{ live.title || "タイトル未設定" }}
                    </h1>

                    <div class="mt-6 grid gap-4 text-zinc-300 md:grid-cols-2">
                        <p>OPEN：{{ live.open_time || "未定" }}</p>
                        <p>START：{{ live.start_time || "未定" }}</p>
                        <p>会場：{{ live.live_house || "未定" }}</p>
                        <p>出演：{{ live.artist || "未定" }}</p>
                    </div>

                    <div
                        class="mt-8 rounded-2xl bg-zinc-900 p-6 leading-8 text-zinc-300"
                    >
                        {{ live.description || "詳細情報はまだありません。" }}
                    </div>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const route = useRoute();

const live = ref(null);
const isLoading = ref(false);
const errorMessage = ref("");

const imageUrl = computed(() => {
    return live.value?.image_path || "/images/hiroshima.png";
});

const fetchLive = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        const response = await fetch(`/api/lives/${route.params.id}`, {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("ライブ詳細の取得に失敗しました。");
        }

        live.value = await response.json();
    } catch (error) {
        errorMessage.value = "ライブ詳細の取得に失敗しました。";
        live.value = null;
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchLive();
});
</script>
