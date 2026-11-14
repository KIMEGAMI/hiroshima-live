<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <div class="mb-8">
                <p class="text-sm font-bold text-red-400">CREATE LIVE EVENT</p>

                <h1 class="mt-2 text-4xl font-black">ライブ情報投稿</h1>
            </div>

            <div
                v-if="loading"
                class="rounded-3xl border border-white/10 bg-white/5 p-8"
            >
                認証確認中...
            </div>

            <form
                v-else
                @submit.prevent="submit"
                class="space-y-6 rounded-3xl border border-white/10 bg-white/5 p-8"
            >
                <div
                    v-if="!user"
                    class="rounded-2xl border border-red-500/30 bg-red-500/10 p-5"
                >
                    <p class="font-bold">ログインなしで投稿できます</p>

                    <p class="mt-2 text-sm leading-6 text-zinc-300">
                        投稿するとすぐに公開されます。あとから内容を修正・管理する場合はログインが必要です。
                    </p>

                    <RouterLink
                        :to="{
                            path: '/login',
                            query: { redirect: '/lives/create' },
                        }"
                        class="mt-4 inline-block text-sm font-bold text-red-300 hover:text-red-200"
                    >
                        ログインして投稿する
                    </RouterLink>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">
                        タイトル
                    </label>

                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        required
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">開催日</label>

                    <input
                        v-model="form.event_date"
                        type="date"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        required
                    />
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            開場時間
                        </label>

                        <input
                            v-model="form.open_time"
                            type="time"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            開演時間
                        </label>

                        <input
                            v-model="form.start_time"
                        type="time"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                    />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">
                        ライブハウス
                    </label>

                    <input
                        v-model="form.live_house"
                        type="text"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">
                        アーティスト
                    </label>

                    <input
                        v-model="form.artist"
                        type="text"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">タグ</label>

                    <p class="mb-3 text-sm text-zinc-400">
                        登録済みタグをクリックすると、このライブ情報にタグを付けられます。
                    </p>

                    <div
                        v-if="tags.length > 0"
                        class="flex flex-wrap gap-2"
                    >
                        <button
                            v-for="tag in tags"
                            :key="tag.id"
                            type="button"
                            class="rounded-full border px-4 py-2 text-sm font-bold transition"
                            :class="
                                selectedTagIds.includes(tag.id)
                                    ? 'border-red-400 bg-red-500 text-white'
                                    : 'border-white/15 bg-zinc-900 text-zinc-300 hover:bg-white/10'
                            "
                            @click="toggleTag(tag.id)"
                        >
                            #{{ tag.name }}
                        </button>
                    </div>

                    <div
                        v-else
                        class="rounded-2xl border border-white/10 bg-zinc-900 p-4 text-sm text-zinc-400"
                    >
                        登録済みタグはまだありません。
                    </div>

                    <div class="mt-5">
                        <label class="mb-2 block text-sm font-bold">
                            当てはまるタグがない場合
                        </label>

                        <div class="flex gap-3">
                            <input
                                v-model="customTagInput"
                                type="text"
                                maxlength="50"
                                placeholder="例：オルタナ、V系、弾き語り"
                                class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                                @keydown.enter.prevent="addCustomTag"
                            />

                            <button
                                type="button"
                                class="shrink-0 rounded-xl border border-white/20 px-4 py-3 text-sm font-bold hover:bg-white/10"
                                @click="addCustomTag"
                            >
                                追加
                            </button>
                        </div>

                        <div
                            v-if="customTags.length > 0"
                            class="mt-3 flex flex-wrap gap-2"
                        >
                            <button
                                v-for="customTag in customTags"
                                :key="customTag"
                                type="button"
                                class="rounded-full border border-emerald-400/50 bg-emerald-500/10 px-4 py-2 text-sm font-bold text-emerald-200 hover:bg-emerald-500/20"
                                @click="removeCustomTag(customTag)"
                            >
                                #{{ customTag }} ×
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">詳細</label>

                    <textarea
                        v-model="form.description"
                        rows="5"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                    ></textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold">画像</label>

                    <input
                        type="file"
                        @change="handleImage"
                        accept=".jpg,.jpeg,.png,.webp,.gif"
                        class="block w-full text-sm"
                    />

                    <p class="mt-2 text-sm text-zinc-400">
                        jpg / jpeg / png / webp / gif 対応<br />
                        最大5MBまでアップロード可能です。
                    </p>
                </div>

                <button
                    type="submit"
                    class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600 disabled:opacity-60"
                    :disabled="isSubmitting"
                >
                    {{ isSubmitting ? "投稿中..." : "投稿する" }}
                </button>

                <p v-if="errorMessage" class="text-sm text-red-400">
                    {{ errorMessage }}
                </p>
            </form>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const router = useRouter();

const loading = ref(true);
const user = ref(null);
const tags = ref([]);
const selectedTagIds = ref([]);
const customTagInput = ref("");
const customTags = ref([]);
const isSubmitting = ref(false);
const errorMessage = ref("");

const form = reactive({
    title: "",
    event_date: "",
    open_time: "",
    start_time: "",
    live_house: "",
    artist: "",
    description: "",
    image: null,
});

const fetchUser = async () => {
    try {
        const response = await axios.get("/api/user");
        user.value = response.data;
    } catch (error) {
        user.value = null;
    } finally {
        loading.value = false;
    }
};

const fetchTags = async () => {
    try {
        const response = await axios.get("/api/tags");
        tags.value = response.data;
    } catch (error) {
        tags.value = [];
    }
};

const toggleTag = (tagId) => {
    if (selectedTagIds.value.includes(tagId)) {
        selectedTagIds.value = selectedTagIds.value.filter((id) => {
            return id !== tagId;
        });

        return;
    }

    selectedTagIds.value.push(tagId);
};

const addCustomTag = () => {
    const tagName = customTagInput.value.trim();

    if (!tagName) {
        return;
    }

    if (customTags.value.includes(tagName)) {
        customTagInput.value = "";
        return;
    }

    customTags.value.push(tagName);
    customTagInput.value = "";
};

const removeCustomTag = (tagName) => {
    customTags.value = customTags.value.filter((customTag) => {
        return customTag !== tagName;
    });
};

const handleImage = (event) => {
    form.image = event.target.files[0];
};

const submit = async () => {
    if (isSubmitting.value) {
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = "";

    try {
        await axios.get("/sanctum/csrf-cookie");

        const formData = new FormData();

        formData.append("title", form.title);
        formData.append("event_date", form.event_date);
        formData.append("open_time", form.open_time);
        formData.append("start_time", form.start_time);
        formData.append("live_house", form.live_house);
        formData.append("artist", form.artist);
        formData.append("description", form.description);

        selectedTagIds.value.forEach((tagId) => {
            formData.append("tag_ids[]", tagId);
        });

        customTags.value.forEach((tagName) => {
            formData.append("custom_tags[]", tagName);
        });

        if (form.image) {
            formData.append("image", form.image);
        }

        await axios.post("/api/lives", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        router.push("/lives");
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "投稿に失敗しました。入力内容を確認してください。";
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    fetchUser();
    fetchTags();
});
</script>
