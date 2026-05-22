<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <div class="max-w-4xl mx-auto px-4 py-10">
            <div class="mb-6">
                <RouterLink
                    to="/"
                    class="text-sm text-zinc-400 hover:text-white transition"
                >
                    ← トップへ戻る
                </RouterLink>
            </div>

            <div
                v-if="live"
                class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-xl"
            >
                <div
                    v-if="imageSrc"
                    class="w-full bg-black flex items-center justify-center p-4"
                >
                    <img
                        :src="imageSrc"
                        :alt="live.title"
                        class="max-w-full max-h-[70vh] object-contain rounded-xl"
                    />
                </div>

                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 text-xs rounded-full bg-red-600">
                            BETA
                        </span>
                    </div>

                    <h1
                        class="text-3xl md:text-4xl font-bold mb-6 leading-tight"
                    >
                        {{ live.title }}
                    </h1>

                    <div class="space-y-4 text-zinc-300">
                        <div
                            v-if="live.event_date"
                            class="flex flex-col md:flex-row md:items-center gap-2"
                        >
                            <div class="w-28 text-zinc-500 font-semibold">
                                DATE
                            </div>

                            <div>
                                {{ formatDate(live.event_date) }}
                            </div>
                        </div>

                        <div
                            v-if="live.open_time && live.open_time !== '未定'"
                            class="flex flex-col md:flex-row md:items-center gap-2"
                        >
                            <div class="w-28 text-zinc-500 font-semibold">
                                OPEN
                            </div>

                            <div>
                                {{ live.open_time }}
                            </div>
                        </div>

                        <div
                            v-if="live.start_time && live.start_time !== '未定'"
                            class="flex flex-col md:flex-row md:items-center gap-2"
                        >
                            <div class="w-28 text-zinc-500 font-semibold">
                                START
                            </div>

                            <div>
                                {{ live.start_time }}
                            </div>
                        </div>

                        <div
                            v-if="live.live_house"
                            class="flex flex-col md:flex-row md:items-center gap-2"
                        >
                            <div class="w-28 text-zinc-500 font-semibold">
                                LIVE HOUSE
                            </div>

                            <div>
                                {{ live.live_house }}
                            </div>
                        </div>

                        <div
                            v-if="live.artist"
                            class="flex flex-col md:flex-row md:items-start gap-2"
                        >
                            <div class="w-28 text-zinc-500 font-semibold">
                                ARTIST
                            </div>

                            <div class="whitespace-pre-wrap">
                                {{ live.artist }}
                            </div>
                        </div>

                        <div
                            v-if="live.description"
                            class="flex flex-col gap-3 pt-4 border-t border-zinc-800"
                        >
                            <div class="text-zinc-500 font-semibold">
                                DESCRIPTION
                            </div>

                            <div
                                class="whitespace-pre-wrap leading-relaxed text-zinc-300"
                            >
                                {{ live.description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20 text-zinc-500">
                読み込み中...
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { RouterLink, useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();

const live = ref(null);

const imageSrc = computed(() => {
    if (!live.value) {
        return "";
    }

    if (live.value.image_url) {
        return live.value.image_url;
    }

    if (live.value.image_path) {
        return live.value.image_path;
    }

    return "";
});

const fetchLive = async () => {
    try {
        const response = await axios.get(`/api/lives/${route.params.id}`);

        live.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

const formatDate = (dateString) => {
    if (!dateString) {
        return "";
    }

    return new Date(dateString).toLocaleDateString("ja-JP", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });
};

onMounted(() => {
    fetchLive();
});
</script>
