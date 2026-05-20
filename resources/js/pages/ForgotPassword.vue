<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <main class="mx-auto flex min-h-screen max-w-md items-center px-6">
            <div
                class="w-full rounded-3xl border border-white/10 bg-white/5 p-8"
            >
                <h1 class="text-3xl font-black">パスワード再設定</h1>

                <p class="mt-3 text-sm text-zinc-400">
                    登録済みのメールアドレスを入力してください。
                </p>

                <form class="mt-6 space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="text-sm font-bold">メールアドレス</label>
                        <input
                            v-model="email"
                            type="email"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600"
                    >
                        再設定メールを送信
                    </button>
                </form>

                <p v-if="message" class="mt-5 text-sm text-green-400">
                    {{ message }}
                </p>

                <p v-if="error" class="mt-5 text-sm text-red-400">
                    {{ error }}
                </p>

                <RouterLink
                    to="/login"
                    class="mt-6 inline-block text-sm text-zinc-400 hover:text-white"
                >
                    ログインへ戻る
                </RouterLink>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from "vue";

const email = ref("");
const message = ref("");
const error = ref("");

const submit = async () => {
    message.value = "";
    error.value = "";

    const response = await fetch("/api/forgot-password", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({
            email: email.value,
        }),
    });

    const data = await response.json();

    if (!response.ok) {
        error.value = data.message || "メール送信に失敗しました。";
        return;
    }

    message.value = data.message;
};
</script>
