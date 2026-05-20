<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <AppHeader />

        <main
            class="mx-auto flex max-w-md items-center justify-center px-6 py-16"
        >
            <div
                class="w-full rounded-3xl border border-white/10 bg-white/5 p-8"
            >
                <p class="text-sm font-bold text-red-400">REGISTER</p>

                <h1 class="mt-2 text-3xl font-black">新規登録</h1>

                <form @submit.prevent="register" class="mt-8 space-y-5">
                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            名前
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                            required
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            メールアドレス
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                            required
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            パスワード
                        </label>

                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3"
                            required
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold">
                            パスワード確認
                        </label>

                        <input
                            v-model="form.password_confirmation"
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
                        登録する
                    </button>
                </form>

                <RouterLink
                    to="/login"
                    class="mt-6 block text-center text-sm text-zinc-400 hover:text-white"
                >
                    すでにアカウントをお持ちの方はこちら
                </RouterLink>
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
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const errorMessage = ref("");

const register = async () => {
    errorMessage.value = "";

    console.log("register submit");

    try {
        console.log("csrf start");
        await axios.get("/sanctum/csrf-cookie");
        console.log("csrf ok");

        console.log("register api start");
        await axios.post("/api/register", {
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });
        console.log("register api ok");

        router.push(route.query.redirect || "/");
    } catch (error) {
        console.error(error);

        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
            return;
        }

        errorMessage.value = "登録に失敗しました。入力内容を確認してください。";
    }
};
</script>
