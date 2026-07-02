<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <div class="mx-auto max-w-4xl px-4 py-10">
            <div class="mb-6">
                <RouterLink
                    to="/"
                    class="text-sm text-zinc-400 transition hover:text-white"
                >
                    ← トップへ戻る
                </RouterLink>
            </div>

            <div
                v-if="live"
                class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-xl"
            >
                <div
                    v-if="imageSrc"
                    class="flex w-full items-center justify-center bg-black p-4"
                >
                    <img
                        :src="imageSrc"
                        :alt="live.title"
                        class="max-h-[70vh] max-w-full rounded-xl object-contain"
                    />
                </div>

                <div class="p-6 md:p-8">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="rounded-full bg-red-600 px-3 py-1 text-xs">
                            BETA
                        </span>
                    </div>

                    <h1
                        class="mb-6 text-3xl font-bold leading-tight md:text-4xl"
                    >
                        {{ live.title }}
                    </h1>

                    <div
                        v-if="live.tags && live.tags.length > 0"
                        class="mb-6 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="tag in live.tags"
                            :key="tag.id"
                            class="rounded-full border border-red-400/40 bg-red-500/10 px-4 py-2 text-sm font-bold text-red-200"
                        >
                            #{{ tag.name }}
                        </span>
                    </div>

                    <div class="space-y-4 text-zinc-300">
                        <div
                            v-if="live.event_date"
                            class="flex flex-col gap-2 md:flex-row md:items-center"
                        >
                            <div class="w-28 font-semibold text-zinc-500">
                                DATE
                            </div>

                            <div>
                                {{ formatDate(live.event_date) }}
                            </div>
                        </div>

                        <div
                            v-if="live.open_time && live.open_time !== '未定'"
                            class="flex flex-col gap-2 md:flex-row md:items-center"
                        >
                            <div class="w-28 font-semibold text-zinc-500">
                                OPEN
                            </div>

                            <div>
                                {{ live.open_time }}
                            </div>
                        </div>

                        <div
                            v-if="live.start_time && live.start_time !== '未定'"
                            class="flex flex-col gap-2 md:flex-row md:items-center"
                        >
                            <div class="w-28 font-semibold text-zinc-500">
                                START
                            </div>

                            <div>
                                {{ live.start_time }}
                            </div>
                        </div>

                        <div
                            v-if="live.live_house"
                            class="flex flex-col gap-2 md:flex-row md:items-center"
                        >
                            <div class="w-28 font-semibold text-zinc-500">
                                LIVE HOUSE
                            </div>

                            <div>
                                {{ live.live_house }}
                            </div>
                        </div>

                        <div
                            v-if="live.artist"
                            class="flex flex-col gap-2 md:flex-row md:items-start"
                        >
                            <div class="w-28 font-semibold text-zinc-500">
                                ARTIST
                            </div>

                            <div class="whitespace-pre-wrap">
                                {{ live.artist }}
                            </div>
                        </div>

                        <div
                            v-if="live.description"
                            class="flex flex-col gap-3 border-t border-zinc-800 pt-4"
                        >
                            <div class="font-semibold text-zinc-500">
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

            <div v-else class="py-20 text-center text-zinc-500">
                読み込み中...
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { RouterLink, useRoute } from "vue-router";
import axios from "axios";
import { DEFAULT_IMAGE, SITE_URL, setSeo } from "../utils/seo";
import { setStructuredData } from "../utils/structuredData";

const route = useRoute();

const live = ref(null);

const siteUrl = SITE_URL;

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

const absoluteImageUrl = computed(() => {
    if (!imageSrc.value) {
        return DEFAULT_IMAGE;
    }

    if (imageSrc.value.startsWith("http")) {
        return imageSrc.value;
    }

    return `${siteUrl}${imageSrc.value}`;
});

const fetchLive = async () => {
    try {
        const response = await axios.get(`/api/lives/${route.params.id}`);

        live.value = response.data;

        setSeo({
            title: `${live.value.title} | 広島ライブ情報 | hiroshima-live`,
            description: createDescription(live.value),
            url: `${siteUrl}/lives/${live.value.id}`,
            image: absoluteImageUrl.value,
            type: "article",
        });

        setStructuredData(createEventStructuredData(live.value));
    } catch (error) {
        console.error(error);
    }
};

const createDescription = (liveItem) => {
    const parts = [
        liveItem.event_date ? formatDate(liveItem.event_date) : "",
        liveItem.live_house || "",
        liveItem.artist || "",
        liveItem.description || "",
    ].filter(Boolean);

    return parts.join(" / ").slice(0, 120);
};

const createEventStructuredData = (liveItem) => {
    return {
        "@context": "https://schema.org",
        "@type": "Event",
        name: liveItem.title,
        startDate: createStartDate(liveItem),
        eventStatus: "https://schema.org/EventScheduled",
        eventAttendanceMode: "https://schema.org/OfflineEventAttendanceMode",
        image: [absoluteImageUrl.value],
        description: createDescription(liveItem),
        url: `${siteUrl}/lives/${liveItem.id}`,
        location: {
            "@type": "Place",
            name: liveItem.live_house || "広島ライブ会場",
            address: {
                "@type": "PostalAddress",
                addressRegion: "広島県",
                addressCountry: "JP",
            },
        },
        performer: {
            "@type": "PerformingGroup",
            name: liveItem.artist || liveItem.title,
        },
        organizer: {
            "@type": "Organization",
            name: "hiroshima-live",
            url: siteUrl,
        },
    };
};

const createStartDate = (liveItem) => {
    if (!liveItem.event_date) {
        return "";
    }

    if (/^\d{2}:\d{2}(:\d{2})?$/.test(liveItem.start_time || "")) {
        const startTime =
            liveItem.start_time.length === 5
                ? `${liveItem.start_time}:00`
                : liveItem.start_time;

        return `${liveItem.event_date}T${startTime}+09:00`;
    }

    return `${liveItem.event_date}T00:00:00+09:00`;
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
