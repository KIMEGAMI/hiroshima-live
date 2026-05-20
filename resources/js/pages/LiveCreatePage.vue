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

            <div
                v-else-if="!user"
                class="rounded-3xl border border-red-500/30 bg-red-500/10 p-8"
            >
                <p class="text-lg font-bold">ログインが必要です</p>

                <RouterLink
                    :to="{
                        path: '/login',
                        query: { redirect: '/lives/create' },
                    }"
                    class="mt-5 inline-block rounded-full bg-red-500 px-5 py-3 font-bold hover:bg-red-600"
                >
                    ログインへ
                </RouterLink>
            </div>

            <form
                v-else
                @submit.prevent="submit"
                class="space-y-6 rounded-3xl border border-white/10 bg-white/5 p-8"
            >
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
                    <label class="mb-2 block text-sm font-bold"> 開催日 </label>

                    <input
                        v-model="form.event_date"
                        type="date"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                        required
                    />
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
                    <label class="mb-2 block text-sm font-bold"> 詳細 </label>

                    <textarea
                        v-model="form.description"
                        rows="5"
                        class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                    ></textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold"> 画像 </label>

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
                    class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                >
                    投稿する
                </button>
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

const form = reactive({
    title: "",
    event_date: "",
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

const handleImage = (event) => {
    form.image = event.target.files[0];
};

const submit = async () => {
    try {
        await axios.get("/sanctum/csrf-cookie");

        const formData = new FormData();

        formData.append("title", form.title);
        formData.append("event_date", form.event_date);
        formData.append("live_house", form.live_house);
        formData.append("artist", form.artist);
        formData.append("description", form.description);

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
        console.error(error.response?.data || error);

        if (error.response?.data?.message) {
            alert(error.response.data.message);
            return;
        }

        alert("投稿に失敗しました");
    }
};

onMounted(() => {
    fetchUser();
});
</script>
