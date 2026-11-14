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

                    <div class="space-y-3 rounded-2xl border border-white/10 bg-zinc-900/70 p-4">
                        <label class="flex cursor-pointer items-start gap-3 text-sm leading-6 text-zinc-200">
                            <input
                                v-model="form.terms_accepted"
                                type="checkbox"
                                class="mt-1 h-5 w-5 rounded border-white/20 bg-zinc-900 text-red-500"
                                required
                            />

                            <span>利用規約に同意します</span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 text-sm leading-6 text-zinc-200">
                            <input
                                v-model="form.privacy_policy_accepted"
                                type="checkbox"
                                class="mt-1 h-5 w-5 rounded border-white/20 bg-zinc-900 text-red-500"
                                required
                            />

                            <span>プライバシーポリシーに同意します</span>
                        </label>
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
    terms_accepted: false,
    privacy_policy_accepted: false,
});

const errorMessage = ref("");

const register = async () => {
    errorMessage.value = "";

    if (!form.terms_accepted || !form.privacy_policy_accepted) {
        errorMessage.value =
            "利用規約とプライバシーポリシーへの同意が必要です。";
        return;
    }

    try {
        await axios.get("/sanctum/csrf-cookie");

        await axios.post("/api/register", {
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation,
            terms_accepted: form.terms_accepted,
            privacy_policy_accepted: form.privacy_policy_accepted,
        });

        router.push(route.query.redirect || "/");
    } catch (error) {
        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
            return;
        }

        errorMessage.value = "登録に失敗しました。入力内容を確認してください。";
    }
};
</script>
