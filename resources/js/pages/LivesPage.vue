<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <p class="text-sm font-bold text-red-400">LIVE EVENTS</p>

            <h1 class="mt-2 text-4xl font-black">新着ライブ一覧</h1>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <LiveCard
                    v-for="live in lives"
                    :key="live.id"
                    :live="live"
                />
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

import AppHeader from '../components/layout/AppHeader.vue';
import AppFooter from '../components/layout/AppFooter.vue';
import LiveCard from '../components/lives/LiveCard.vue';

const lives = ref([]);

onMounted(async () => {
    const response = await fetch('/api/lives');
    lives.value = await response.json();
});
</script>