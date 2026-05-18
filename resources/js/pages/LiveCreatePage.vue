<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <RouterLink
                to="/lives"
                class="text-sm font-bold text-zinc-400 hover:text-white"
            >
                ← ライブ一覧へ戻る
            </RouterLink>

            <div
                class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl"
            >
                <p class="text-sm font-bold text-red-400">CREATE LIVE EVENT</p>

                <h1 class="mt-2 text-4xl font-black">ライブ情報を投稿</h1>

                <form class="mt-8 space-y-5" @submit.prevent="submitForm">
                    <div>
                        <label
                            class="mb-2 block text-sm font-bold text-zinc-300"
                        >
                            タイトル
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-bold text-zinc-300"
                        >
                            開催日
                        </label>
                        <input
                            v-model="form.event_date"
                            type="date"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                            required
                        />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-zinc-300"
                            >
                                OPEN時間
                            </label>
                            <input
                                v-model="form.open_time"
                                type="time"
                                class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-zinc-300"
                            >
                                START時間
                            </label>
                            <input
                                v-model="form.start_time"
                                type="time"
                                class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-bold text-zinc-300"
                        >
                            ライブハウス名
                        </label>
                        <input
                            v-model="form.live_house"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-bold text-zinc-300"
                        >
                            出演者
                        </label>
                        <input
                            v-model="form.artist"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-bold text-zinc-300"
                        >
                            画像
                        </label>
                        <input
                            type="file"
                            accept="image/*"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                            @change="handleImageChange"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-bold text-zinc-300"
                        >
                            詳細説明
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="6"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white outline-none focus:border-red-500"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-red-500 px-6 py-4 font-bold text-white hover:bg-red-600"
                    >
                        投稿する
                    </button>

                    <p v-if="errorMessage" class="text-sm text-red-400">
                        {{ errorMessage }}
                    </p>
                </form>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

import AppHeader from '../components/layout/AppHeader.vue';
import AppFooter from '../components/layout/AppFooter.vue';

const router = useRouter();
const errorMessage = ref('');
const selectedImage = ref(null);

const form = reactive({
    title: '',
    event_date: '',
    open_time: '',
    start_time: '',
    live_house: '',
    artist: '',
    description: '',
});

const handleImageChange = (event) => {
    selectedImage.value = event.target.files[0];
};

const submitForm = async () => {
    errorMessage.value = '';

    const formData = new FormData();

    formData.append('title', form.title);
    formData.append('event_date', form.event_date);
    formData.append('open_time', form.open_time || '');
    formData.append('start_time', form.start_time || '');
    formData.append('live_house', form.live_house || '');
    formData.append('artist', form.artist || '');
    formData.append('description', form.description || '');

    if (selectedImage.value) {
        formData.append('image', selectedImage.value);
    }

    const response = await fetch('/api/lives', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
        },
        body: formData,
    });

    if (!response.ok) {
        errorMessage.value = '投稿に失敗しました。入力内容を確認してください。';
        return;
    }

    router.push('/lives');
};
</script>