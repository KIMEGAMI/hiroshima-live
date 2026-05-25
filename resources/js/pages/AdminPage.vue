<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main class="mx-auto max-w-7xl px-6 py-12">
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-bold text-purple-400">ADMIN MODE</p>
                    <h1 class="mt-2 text-4xl font-black">管理者モード</h1>
                    <p class="mt-4 text-zinc-400">
                        全てのライブ投稿の編集・削除、ユーザー削除を行えます。
                    </p>
                </div>

                <button
                    type="button"
                    class="rounded-full border border-white/20 px-5 py-3 text-sm font-bold hover:bg-white/10"
                    @click="fetchAdminData"
                >
                    再読み込み
                </button>
            </div>

            <div
                v-if="loading"
                class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400"
            >
                管理者情報を確認中です。
            </div>

            <div
                v-else-if="errorMessage"
                class="rounded-3xl border border-red-500/30 bg-red-500/10 p-8"
            >
                <p class="font-bold text-red-300">{{ errorMessage }}</p>
                <RouterLink
                    to="/login?redirect=/admin"
                    class="mt-5 inline-block rounded-full bg-red-500 px-5 py-3 font-bold hover:bg-red-600"
                >
                    ログインへ
                </RouterLink>
            </div>

            <div v-else class="space-y-10">
                <section class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-bold text-zinc-400">USERS</p>
                        <p class="mt-3 text-4xl font-black">{{ dashboard.users_count }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-bold text-zinc-400">LIVE POSTS</p>
                        <p class="mt-3 text-4xl font-black">{{ dashboard.lives_count }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-bold text-zinc-400">ADMINS</p>
                        <p class="mt-3 text-4xl font-black">{{ dashboard.admin_users_count }}</p>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-purple-400">USERS</p>
                            <h2 class="mt-1 text-2xl font-black">ユーザー管理</h2>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px] text-left text-sm">
                            <thead class="border-b border-white/10 text-zinc-400">
                                <tr>
                                    <th class="py-3 pr-4">ID</th>
                                    <th class="py-3 pr-4">名前</th>
                                    <th class="py-3 pr-4">メール</th>
                                    <th class="py-3 pr-4">権限</th>
                                    <th class="py-3 pr-4">登録日</th>
                                    <th class="py-3 text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="user in users" :key="user.id">
                                    <td class="py-4 pr-4 text-zinc-400">{{ user.id }}</td>
                                    <td class="py-4 pr-4 font-bold">{{ user.name }}</td>
                                    <td class="py-4 pr-4 text-zinc-300">{{ user.email }}</td>
                                    <td class="py-4 pr-4">
                                        <span
                                            :class="user.is_admin ? 'bg-purple-500/20 text-purple-200' : 'bg-white/10 text-zinc-300'"
                                            class="rounded-full px-3 py-1 text-xs font-bold"
                                        >
                                            {{ user.is_admin ? '管理者' : '一般' }}
                                        </span>
                                    </td>
                                    <td class="py-4 pr-4 text-zinc-400">{{ formatDateTime(user.created_at) }}</td>
                                    <td class="py-4 text-right">
                                        <button
                                            type="button"
                                            :disabled="user.is_admin"
                                            class="rounded-full bg-red-500 px-4 py-2 text-xs font-bold hover:bg-red-600 disabled:cursor-not-allowed disabled:bg-zinc-700 disabled:text-zinc-400"
                                            @click="deleteUser(user)"
                                        >
                                            削除
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <div class="mb-6">
                        <p class="text-sm font-bold text-purple-400">LIVE POSTS</p>
                        <h2 class="mt-1 text-2xl font-black">ライブ投稿管理</h2>
                    </div>

                    <div class="grid gap-6">
                        <article
                            v-for="live in lives"
                            :key="live.id"
                            class="rounded-3xl border border-white/10 bg-zinc-950/70 p-5"
                        >
                            <div class="grid gap-5 lg:grid-cols-[140px_1fr]">
                                <img
                                    :src="live.image_path || '/images/hiroshima.png'"
                                    :alt="live.title"
                                    class="aspect-[2/3] w-full rounded-2xl object-cover lg:w-36"
                                />

                                <div class="space-y-4">
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-xs font-bold text-zinc-400">タイトル</label>
                                            <input v-model="live.title" type="text" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold text-zinc-400">開催日</label>
                                            <input v-model="live.event_date" type="date" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold text-zinc-400">OPEN</label>
                                            <input v-model="live.open_time" type="time" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold text-zinc-400">START</label>
                                            <input v-model="live.start_time" type="time" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold text-zinc-400">ライブハウス</label>
                                            <input v-model="live.live_house" type="text" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold text-zinc-400">アーティスト</label>
                                            <input v-model="live.artist" type="text" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3" />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-bold text-zinc-400">詳細</label>
                                        <textarea v-model="live.description" rows="4" class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"></textarea>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-4">
                                        <p class="text-xs text-zinc-400">
                                            投稿者：{{ live.user?.name || '不明' }} / {{ live.user?.email || '不明' }}
                                        </p>

                                        <div class="flex flex-wrap gap-3">
                                            <RouterLink
                                                :to="`/lives/${live.id}`"
                                                class="rounded-full border border-white/20 px-4 py-2 text-sm font-bold hover:bg-white/10"
                                            >
                                                詳細
                                            </RouterLink>
                                            <button
                                                type="button"
                                                class="rounded-full bg-purple-500 px-4 py-2 text-sm font-bold hover:bg-purple-600"
                                                @click="updateLive(live)"
                                            >
                                                保存
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded-full bg-red-500 px-4 py-2 text-sm font-bold hover:bg-red-600"
                                                @click="deleteLive(live)"
                                            >
                                                削除
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <div v-if="lives.length === 0" class="rounded-3xl border border-white/10 bg-white/5 p-8 text-zinc-400">
                            投稿はありません。
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const loading = ref(true);
const errorMessage = ref("");
const dashboard = ref({ users_count: 0, lives_count: 0, admin_users_count: 0 });
const users = ref([]);
const lives = ref([]);

const fetchAdminData = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        const [dashboardResponse, usersResponse, livesResponse] = await Promise.all([
            axios.get("/api/admin/dashboard"),
            axios.get("/api/admin/users"),
            axios.get("/api/admin/lives"),
        ]);

        dashboard.value = dashboardResponse.data;
        users.value = usersResponse.data;
        lives.value = livesResponse.data;
    } catch (error) {
        errorMessage.value = error.response?.status === 403
            ? "管理者権限がありません。"
            : "管理者情報の取得に失敗しました。";
    } finally {
        loading.value = false;
    }
};

const updateLive = async (live) => {
    try {
        await axios.get("/sanctum/csrf-cookie");

        const formData = new FormData();
        formData.append("title", live.title || "");
        formData.append("event_date", live.event_date || "");
        formData.append("open_time", live.open_time || "");
        formData.append("start_time", live.start_time || "");
        formData.append("live_house", live.live_house || "");
        formData.append("artist", live.artist || "");
        formData.append("description", live.description || "");

        await axios.post(`/api/admin/lives/${live.id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        alert("保存しました。");
        await fetchAdminData();
    } catch (error) {
        alert(error.response?.data?.message || "保存に失敗しました。");
    }
};

const deleteLive = async (live) => {
    if (!confirm(`「${live.title}」を削除します。よろしいですか？`)) {
        return;
    }

    try {
        await axios.delete(`/api/admin/lives/${live.id}`);
        await fetchAdminData();
    } catch (error) {
        alert(error.response?.data?.message || "削除に失敗しました。");
    }
};

const deleteUser = async (user) => {
    if (!confirm(`ユーザー「${user.name}」を削除します。よろしいですか？`)) {
        return;
    }

    try {
        await axios.delete(`/api/admin/users/${user.id}`);
        await fetchAdminData();
    } catch (error) {
        alert(error.response?.data?.message || "ユーザー削除に失敗しました。");
    }
};

const formatDateTime = (value) => {
    if (!value) {
        return "";
    }

    return new Date(value).toLocaleString("ja-JP");
};

onMounted(() => {
    fetchAdminData();
});
</script>
