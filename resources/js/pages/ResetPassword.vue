<template>
    <div class="min-h-screen bg-zinc-950 text-white">
        <main class="mx-auto flex min-h-screen max-w-md items-center px-6">
            <div
                class="w-full rounded-3xl border border-white/10 bg-white/5 p-8"
            >
                <h1 class="text-3xl font-black">新しいパスワード</h1>

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

                    <div>
                        <label class="text-sm font-bold"
                            >新しいパスワード</label
                        >
                        <input
                            v-model="password"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white"
                            required
                        />
                    </div>

                    <div>
                        <label class="text-sm font-bold"
                            >新しいパスワード確認</label
                        >
                        <input
                            v-model="passwordConfirmation"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900 px-4 py-3 text-white"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-red-500 px-6 py-3 font-bold hover:bg-red-600 disabled:opacity-60"
                        :disabled="isSubmitting"
                    >
                        {{ isSubmitting ? "変更中..." : "パスワードを変更" }}
                    </button>
                </form>

                <p v-if="message" class="mt-5 text-sm text-green-400">
                    {{ message }}
                </p>

                <p v-if="error" class="mt-5 text-sm text-red-400">
                    {{ error }}
                </p>
            </div>
        </main>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const token = ref("");
const email = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const message = ref("");
const error = ref("");
const isSubmitting = ref(false);
const redirectDelayMs = 1200;

const getQueryString = (value) => {
    if (Array.isArray(value)) {
        return value[0] || "";
    }

    return typeof value === "string" ? value : "";
};

onMounted(() => {
    token.value = getQueryString(route.query.token);
    email.value = getQueryString(route.query.email);
});

const submit = async () => {
    if (isSubmitting.value) {
        return;
    }

    message.value = "";
    error.value = "";
    isSubmitting.value = true;

    try {
        const response = await fetch("/api/reset-password", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                token: token.value,
                email: email.value,
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            error.value = data.message || "パスワード変更に失敗しました。";
            return;
        }

        message.value = data.message;

        setTimeout(() => {
            router.push("/login");
        }, redirectDelayMs);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
