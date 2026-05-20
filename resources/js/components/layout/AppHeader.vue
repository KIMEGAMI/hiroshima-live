<template>
    <header class="border-b border-white/10 bg-zinc-950/80 backdrop-blur">
        <div
            class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4"
        >
            <RouterLink to="/" class="text-2xl font-black tracking-wide">
                Hiroshima Live
            </RouterLink>

            <nav class="flex items-center gap-5 text-sm font-bold">
                <RouterLink to="/lives" class="text-zinc-300 hover:text-white">
                    ライブ一覧
                </RouterLink>

                <RouterLink
                    to="/calendar"
                    class="text-zinc-300 hover:text-white"
                >
                    カレンダー
                </RouterLink>

                <RouterLink
                    to="/lives/create"
                    class="text-zinc-300 hover:text-white"
                >
                    投稿
                </RouterLink>

                <template v-if="loading">
                    <span class="text-zinc-500"> 読み込み中... </span>
                </template>

                <template v-else-if="user">
                    <span class="text-zinc-300">
                        {{ user.name }}
                    </span>

                    <button
                        @click="logout"
                        class="rounded-full bg-red-500 px-4 py-2 hover:bg-red-600"
                    >
                        ログアウト
                    </button>
                </template>

                <template v-else>
                    <RouterLink
                        to="/login"
                        class="text-zinc-300 hover:text-white"
                    >
                        ログイン
                    </RouterLink>

                    <RouterLink
                        to="/register"
                        class="rounded-full bg-red-500 px-4 py-2 hover:bg-red-600"
                    >
                        新規登録
                    </RouterLink>
                </template>
            </nav>
        </div>
    </header>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const loading = ref(true);
const user = ref(null);

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

const logout = async () => {
    try {
        await axios.post("/api/logout");

        user.value = null;

        router.push("/");
        window.location.reload();
    } catch (error) {
        console.error(error);
    }
};

onMounted(() => {
    fetchUser();
});
</script>
