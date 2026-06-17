<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-red-400">LIVE EVENTS</p>

                    <h1 class="mt-2 text-4xl font-black">ライブ一覧</h1>

                    <p class="mt-4 text-zinc-400">
                        広島のライブ情報を検索できます。
                    </p>
                </div>

                <RouterLink
                    to="/lives/create"
                    class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                >
                    ライブを投稿
                </RouterLink>
            </div>

            <form
                class="mb-8 rounded-3xl border border-white/10 bg-white/5 p-6"
                @submit.prevent="searchLives"
            >
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-3">
                        <label class="mb-2 block text-sm font-bold">
                            キーワード
                        </label>

                        <input
                            v-model="filters.keyword"
                            type="text"
                            placeholder="タイトル、説明、会場、アーティスト、タグをまとめて検索"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            ライブハウス名
                        </label>

                        <input
                            v-model="filters.live_house"
                            type="text"
                            placeholder="例：ALMIGHTY"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            アーティスト名
                        </label>

                        <input
                            v-model="filters.artist"
                            type="text"
                            placeholder="例：バンド名"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            日付
                        </label>

                        <input
                            v-model="filters.date"
                            type="date"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            タグ
                        </label>

                        <select
                            v-model="filters.tag"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        >
                            <option value="">すべて</option>
                            <option
                                v-for="tag in tags"
                                :key="tag.id"
                                :value="tag.name"
                            >
                                #{{ tag.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <label
                            class="flex w-full cursor-pointer items-center gap-3 rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        >
                            <input
                                v-model="filters.future"
                                type="checkbox"
                                class="h-5 w-5 rounded border-white/20 bg-zinc-900"
                            />

                            <span class="text-sm font-bold">
                                今後のライブのみ
                            </span>
                        </label>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    <button
                        type="submit"
                        class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                    >
                        検索する
                    </button>

                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-6 py-3 font-bold hover:bg-white/10"
                        @click="resetSearch"
                    >
                        条件クリア
                    </button>
                </div>
            </form>

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
                条件に合うライブ情報はありません。
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";
import LiveCard from "../components/lives/LiveCard.vue";

const route = useRoute();
const router = useRouter();

const lives = ref([]);
const tags = ref([]);
const isLoading = ref(false);
const errorMessage = ref("");

const filters = reactive({
    keyword: "",
    live_house: "",
    artist: "",
    date: "",
    tag: "",
    future: false,
});

const selectedDate = computed(() => {
    return route.query.date || "";
});

const selectedDateLabel = computed(() => {
    if (!selectedDate.value) {
        return "";
    }

    return selectedDate.value.replaceAll("-", "/");
});

const syncFiltersFromQuery = () => {
    filters.keyword = route.query.keyword || "";
    filters.live_house = route.query.live_house || "";
    filters.artist = route.query.artist || "";
    filters.date = route.query.date || "";
    filters.tag = route.query.tag || "";
    filters.future = route.query.future === "1";
};

const buildQueryString = () => {
    const params = new URLSearchParams();

    if (filters.keyword) {
        params.append("keyword", filters.keyword);
    }

    if (filters.live_house) {
        params.append("live_house", filters.live_house);
    }

    if (filters.artist) {
        params.append("artist", filters.artist);
    }

    if (filters.date) {
        params.append("date", filters.date);
    }

    if (filters.tag) {
        params.append("tag", filters.tag);
    }

    if (filters.future) {
        params.append("future", "1");
    }

    const queryString = params.toString();

    return queryString ? `?${queryString}` : "";
};

const fetchTags = async () => {
    try {
        const response = await fetch("/api/tags", {
            headers: {
                Accept: "application/json",
            },
        });

        tags.value = await response.json();
    } catch (error) {
        tags.value = [];
    }
};

const fetchLives = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        syncFiltersFromQuery();

        const response = await fetch(`/api/lives${buildQueryString()}`, {
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

const searchLives = () => {
    router.push({
        path: "/lives",
        query: {
            ...(filters.keyword ? { keyword: filters.keyword } : {}),
            ...(filters.live_house ? { live_house: filters.live_house } : {}),
            ...(filters.artist ? { artist: filters.artist } : {}),
            ...(filters.date ? { date: filters.date } : {}),
            ...(filters.tag ? { tag: filters.tag } : {}),
            ...(filters.future ? { future: "1" } : {}),
        },
    });
};

const resetSearch = () => {
    filters.keyword = "";
    filters.live_house = "";
    filters.artist = "";
    filters.date = "";
    filters.tag = "";
    filters.future = false;

    router.push("/lives");
};

watch(
    () => route.query,
    () => {
        fetchLives();
    },
    { deep: true },
);

onMounted(async () => {
    syncFiltersFromQuery();

    await fetchTags();
    await fetchLives();
});
</script>