<template>
    <article
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl transition hover:-translate-y-1 hover:bg-white/10"
    >
        <div class="bg-zinc-900">
            <img
                :src="imageUrl"
                :alt="live.title"
                class="aspect-[2/3] w-full object-cover"
            />
        </div>

        <div class="p-5">
            <p class="text-sm font-bold text-red-400">
                {{ live.event_date }}
            </p>

            <h3 class="mt-2 text-xl font-bold text-white">
                {{ live.title }}
            </h3>

            <p class="mt-2 text-sm text-zinc-400">
                {{ live.live_house }}
            </p>

            <div
                v-if="live.tags && live.tags.length > 0"
                class="mt-3 flex flex-wrap gap-2"
            >
                <span
                    v-for="tag in live.tags"
                    :key="tag.id"
                    class="rounded-full border border-red-400/40 bg-red-500/10 px-3 py-1 text-xs font-bold text-red-200"
                >
                    #{{ tag.name }}
                </span>
            </div>

            <p class="mt-3 line-clamp-2 text-sm leading-6 text-zinc-300">
                {{ live.description }}
            </p>

            <button
                type="button"
                class="mt-5 inline-block rounded-full bg-red-500 px-4 py-2 text-sm font-bold text-white hover:bg-red-600"
                @click="goToDetail"
            >
                詳細を見る
            </button>
        </div>
    </article>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";

const props = defineProps({
    live: {
        type: Object,
        required: true,
    },
});

const router = useRouter();

const imageUrl = computed(() => {
    return props.live.image_path || "/images/hiroshima.png";
});

const goToDetail = () => {
    router.push(`/lives/${props.live.id}`);
};
</script>