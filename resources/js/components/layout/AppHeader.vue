<template>
    <header
        class="sticky top-0 z-50 border-b border-white/10 bg-zinc-950/80 backdrop-blur"
    >
        <div
            class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4"
        >
            <RouterLink
                to="/"
                class="text-xl font-black tracking-wide text-white"
            >
                Hiroshima<span class="text-red-500">Live</span>
                <h2 class="text-xl font-bold">
                    <span class="ml-2 text-sm text-red-400 font-normal">
                        [β版]
                    </span>
                </h2>
            </RouterLink>

            <nav
                class="hidden items-center gap-6 text-sm text-zinc-300 md:flex"
            >
                <RouterLink to="/" class="hover:text-white">トップ</RouterLink>

                <RouterLink to="/lives" class="hover:text-white">
                    新着ライブ
                </RouterLink>

                <RouterLink to="/calendar" class="hover:text-white">
                    カレンダー
                </RouterLink>

                <template v-if="user">
                    <RouterLink
                        to="/lives/create"
                        class="rounded-full bg-red-500 px-4 py-2 font-bold text-white hover:bg-red-600"
                    >
                        ライブ投稿
                    </RouterLink>

                    <RouterLink to="/mypage" class="hover:text-white">
                        マイページ
                    </RouterLink>

                    <RouterLink
                        v-if="user.is_admin"
                        to="/admin"
                        class="rounded-full border border-purple-400/50 px-4 py-2 font-bold text-purple-200 hover:bg-purple-500/20"
                    >
                        管理者
                    </RouterLink>

                    <button
                        type="button"
                        class="hover:text-white"
                        @click="logout"
                    >
                        ログアウト
                    </button>
                </template>

                <template v-else>
                    <RouterLink to="/login" class="hover:text-white">
                        ログイン
                    </RouterLink>

                    <RouterLink
                        to="/register"
                        class="rounded-full border border-white/20 px-4 py-2 font-bold hover:bg-white/10"
                    >
                        新規登録
                    </RouterLink>
                </template>
            </nav>
        </div>
    </header>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const user = ref(null);

const fetchUser = async () => {
    try {
        const response = await axios.get("/api/user");
        user.value = response.data;
    } catch (error) {
        user.value = null;
    }
};

const handleAuthChanged = (event) => {
    if (event.detail?.user) {
        user.value = event.detail.user;
        return;
    }

    fetchUser();
};

const logout = async () => {
    try {
        await axios.post("/api/logout");
    } catch (error) {
        console.error(error);
    }

    user.value = null;

    window.dispatchEvent(
        new CustomEvent("auth-changed", {
            detail: {
                user: null,
            },
        })
    );

    router.push("/");
};

watch(
    () => route.fullPath,
    () => {
        fetchUser();
    }
);

onMounted(() => {
    fetchUser();
    window.addEventListener("auth-changed", handleAuthChanged);
});

onBeforeUnmount(() => {
    window.removeEventListener("auth-changed", handleAuthChanged);
});
</script>