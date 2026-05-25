<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main
            class="mx-auto flex max-w-md items-center justify-center px-6 py-16"
        >
            <div
                class="w-full rounded-3xl border border-white/10 bg-white/5 p-8"
            >
                <p class="text-sm font-bold text-red-400">LOGIN</p>

                <h1 class="mt-2 text-3xl font-black">ログイン</h1>

                <form @submit.prevent="login" class="mt-8 space-y-5">
                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            メールアドレス / 管理者ID
                        </label>

                        <input
                            v-model="form.email"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                            required
                        />
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-sm font-bold">
                                パスワード
                            </label>

                            <RouterLink
                                to="/forgot-password"
                                class="text-sm font-bold text-red-400 hover:text-red-300"
                            >
                                パスワードを忘れた方
                            </RouterLink>
                        </div>

                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                            required
                        />
                    </div>

                    <p v-if="errorMessage" class="text-sm text-red-400">
                        {{ errorMessage }}
                    </p>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                    >
                        ログイン
                    </button>
                </form>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const route = useRoute();
const router = useRouter();

const form = reactive({
    email: "",
    password: "",
});

const errorMessage = ref("");

const login = async () => {
    errorMessage.value = "";

    try {
        await axios.get("/sanctum/csrf-cookie");

        await axios.post("/api/login", {
            email: form.email,
            password: form.password,
        });

        router.push(route.query.redirect || "/");
    } catch (error) {
        errorMessage.value = "ログインに失敗しました。";
    }
};
</script>
