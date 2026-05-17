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
                v-if="live"
                class="mt-8 overflow-hidden rounded-3xl border border-white/10 bg-white/5"
            >
                <img
                    :src="live.image_path"
                    :alt="live.title"
                    class="h-80 w-full object-cover"
                />

                <div class="p-8">
                    <p class="text-sm font-bold text-red-400">
                        {{ live.event_date }}
                    </p>

                    <h1 class="mt-3 text-4xl font-black">
                        {{ live.title }}
                    </h1>

                    <div class="mt-6 grid gap-4 text-zinc-300 md:grid-cols-2">
                        <p>OPEN：{{ live.open_time }}</p>
                        <p>START：{{ live.start_time }}</p>
                        <p>会場：{{ live.live_house }}</p>
                        <p>出演：{{ live.artist }}</p>
                    </div>

                    <div
                        class="mt-8 rounded-2xl bg-zinc-900 p-6 leading-8 text-zinc-300"
                    >
                        {{ live.description }}
                    </div>
                </div>
            </div>

            <div v-else class="mt-8 text-zinc-400">読み込み中です...</div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const route = useRoute();
const live = ref(null);

onMounted(async () => {
    const response = await fetch(`/api/lives/${route.params.id}`);
    live.value = await response.json();
});
</script>
