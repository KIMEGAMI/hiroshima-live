<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-red-400">MY PAGE</p>
                    <h1 class="mt-2 text-4xl font-black">マイページ</h1>
                    <p class="mt-4 text-zinc-400">
                        自分が投稿したライブ情報を編集できます。
                    </p>
                </div>

                <RouterLink
                    to="/lives/create"
                    class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                >
                    新しく投稿する
                </RouterLink>
            </div>

            <div
                v-if="loading"
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                読み込み中です。
            </div>

            <div
                v-else-if="!user"
                class="rounded-3xl border border-red-500/30 bg-red-500/10 p-8"
            >
                <p class="font-bold">ログインが必要です。</p>

                <RouterLink
                    :to="{ path: '/login', query: { redirect: '/mypage' } }"
                    class="mt-5 inline-block rounded-full bg-red-500 px-5 py-3 font-bold hover:bg-red-600"
                >
                    ログインへ
                </RouterLink>
            </div>

            <div v-else-if="lives.length > 0" class="grid gap-6">
                <article
                    v-for="live in lives"
                    :key="live.id"
                    class="grid gap-5 rounded-3xl border border-white/10 bg-white/5 p-5 md:grid-cols-[160px_1fr_auto]"
                >
                    <img
                        :src="live.image_path || '/images/hiroshima.png'"
                        :alt="live.title"
                        class="aspect-[2/3] w-full rounded-2xl object-cover md:w-40"
                    />

                    <div>
                        <p class="text-sm font-bold text-red-400">
                            {{ live.event_date }}
                        </p>

                        <h2 class="mt-2 text-2xl font-black">
                            {{ live.title }}
                        </h2>

                        <p class="mt-2 text-zinc-400">
                            {{ live.live_house || "会場未定" }}
                        </p>

                        <p
                            class="mt-3 line-clamp-2 text-sm leading-6 text-zinc-300"
                        >
                            {{ live.description || "説明はありません。" }}
                        </p>
                    </div>

                    <div
                        class="flex items-center gap-3 md:flex-col md:items-end md:justify-center"
                    >
                        <RouterLink
                            :to="`/lives/${live.id}`"
                            class="rounded-full border border-white/20 px-4 py-2 text-sm font-bold hover:bg-white/10"
                        >
                            詳細
                        </RouterLink>

                        <RouterLink
                            :to="`/lives/${live.id}/edit`"
                            class="rounded-full bg-red-500 px-4 py-2 text-sm font-bold hover:bg-red-600"
                        >
                            編集
                        </RouterLink>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                まだ投稿したライブ情報はありません。
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const loading = ref(true);
const user = ref(null);
const lives = ref([]);

const fetchData = async () => {
    loading.value = true;

    try {
        const userResponse = await axios.get("/api/user");
        user.value = userResponse.data;

        const livesResponse = await axios.get("/api/my/lives");
        lives.value = livesResponse.data;
    } catch (error) {
        user.value = null;
        lives.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
});
</script>
