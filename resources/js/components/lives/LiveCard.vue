<template>
    <article
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl transition hover:-translate-y-1 hover:bg-white/10"
    >
        <img
            :src="live.image"
            :alt="live.title"
            class="h-44 w-full object-cover"
        />

        <div class="p-5">
            <p class="text-sm font-bold text-red-400">
                {{ live.date }}
            </p>

            <h3 class="mt-2 text-xl font-bold text-white">
                {{ live.title }}
            </h3>

            <p class="mt-2 text-sm text-zinc-400">
                {{ live.house }}
            </p>

            <p class="mt-3 line-clamp-2 text-sm leading-6 text-zinc-300">
                {{ live.description }}
            </p>

            <a
                href="#"
                class="mt-5 inline-block rounded-full bg-red-500 px-4 py-2 text-sm font-bold text-white hover:bg-red-600"
            >
                詳細を見る
            </a>
        </div>
    </article>
</template>

<script setup>
defineProps({
    live: {
        type: Object,
        required: true,
    },
});
</script>
