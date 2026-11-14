<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-6xl px-6 py-12">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-red-400">MY PAGE</p>
                    <h1 class="mt-2 text-4xl font-black">マイページ</h1>
                    <p class="mt-4 text-zinc-400">
                        自分が投稿したライブ情報を編集できます。
                    </p>
                </div>

                <RouterLink
                    to="/lives/create"
                    class="rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                >
                    新しく投稿する
                </RouterLink>
            </div>

            <div
                v-if="loading"
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                読み込み中です。
            </div>

            <div
                v-else-if="!user"
                class="rounded-3xl border border-red-500/30 bg-red-500/10 p-8"
            >
                <p class="font-bold">ログインが必要です。</p>

                <RouterLink
                    :to="{ path: '/login', query: { redirect: '/mypage' } }"
                    class="mt-5 inline-block rounded-full bg-red-500 px-5 py-3 font-bold hover:bg-red-600"
                >
                    ログインへ
                </RouterLink>
            </div>

            <div v-else-if="lives.length > 0" class="grid gap-6">
                <article
                    v-for="live in lives"
                    :key="live.id"
                    class="grid gap-5 rounded-3xl border border-white/10 bg-white/5 p-5 md:grid-cols-[160px_1fr_auto]"
                >
                    <img
                        :src="live.image_path || '/images/hiroshima.png'"
                        :alt="live.title"
                        class="aspect-[2/3] w-full rounded-2xl object-cover md:w-40"
                    />

                    <div>
                        <p class="text-sm font-bold text-red-400">
                            {{ live.event_date }}
                        </p>

                        <h2 class="mt-2 text-2xl font-black">
                            {{ live.title }}
                        </h2>

                        <p class="mt-2 text-zinc-400">
                            {{ live.live_house || "会場未定" }}
                        </p>

                        <p
                            class="mt-3 line-clamp-2 text-sm leading-6 text-zinc-300"
                        >
                            {{ live.description || "説明はありません。" }}
                        </p>
                    </div>

                    <div
                        class="flex items-center gap-3 md:flex-col md:items-end md:justify-center"
                    >
                        <RouterLink
                            :to="`/lives/${live.id}`"
                            class="rounded-full border border-white/20 px-4 py-2 text-sm font-bold hover:bg-white/10"
                        >
                            詳細
                        </RouterLink>

                        <RouterLink
                            :to="`/lives/${live.id}/edit`"
                            class="rounded-full bg-red-500 px-4 py-2 text-sm font-bold hover:bg-red-600"
                        >
                            編集
                        </RouterLink>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                まだ投稿したライブ情報はありません。
            </div>
            <section
                v-if="user"
                class="mt-10 rounded-3xl border border-red-500/30 bg-red-500/10 p-8"
            >
                <p class="text-sm font-bold text-red-300">DANGER ZONE</p>
                <h2 class="mt-2 text-2xl font-black">アカウント削除</h2>
                <p class="mt-3 text-sm leading-6 text-red-100/80">
                    アカウントをDBから完全削除します。削除後は元に戻せません。
                </p>

                <form @submit.prevent="deleteAccount" class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            現在のパスワード
                        </label>

                        <input
                            v-model="deletePassword"
                            type="password"
                            class="w-full rounded-xl border border-red-400/30 bg-zinc-950 px-4 py-3"
                            autocomplete="current-password"
                            required
                        />
                    </div>

                    <p v-if="deleteMessage" class="text-sm text-red-200">
                        {{ deleteMessage }}
                    </p>

                    <button
                        type="submit"
                        class="rounded-full bg-red-600 px-6 py-3 font-bold hover:bg-red-700 disabled:opacity-60"
                        :disabled="isDeleting"
                    >
                        {{
                            isDeleting
                                ? "削除しています..."
                                : "アカウントを削除"
                        }}
                    </button>
                </form>
            </section>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const router = useRouter();

const loading = ref(true);
const user = ref(null);
const lives = ref([]);
const deletePassword = ref("");
const deleteMessage = ref("");
const isDeleting = ref(false);

const fetchData = async () => {
    loading.value = true;

    try {
        const userResponse = await axios.get("/api/user");
        user.value = userResponse.data;

        const livesResponse = await axios.get("/api/my/lives");
        lives.value = livesResponse.data;
    } catch (error) {
        user.value = null;
        lives.value = [];
    } finally {
        loading.value = false;
    }
};

const deleteAccount = async () => {
    if (isDeleting.value) {
        return;
    }

    if (!deletePassword.value) {
        deleteMessage.value = "現在のパスワードを入力してください。";
        return;
    }

    const confirmed = window.confirm(
        "アカウントを完全削除します。削除後は元に戻せません。実行しますか？"
    );

    if (!confirmed) {
        return;
    }

    isDeleting.value = true;
    deleteMessage.value = "";

    try {
        await axios.delete("/api/user", {
            data: {
                password: deletePassword.value,
            },
        });

        user.value = null;
        lives.value = [];
        deletePassword.value = "";

        window.dispatchEvent(
            new CustomEvent("auth-changed", {
                detail: {
                    user: null,
                },
            })
        );

        router.push("/");
    } catch (error) {
        deleteMessage.value =
            error.response?.data?.message ||
            "アカウント削除に失敗しました。";
    } finally {
        isDeleting.value = false;
    }
};

onMounted(() => {
    fetchData();
});
</script>
