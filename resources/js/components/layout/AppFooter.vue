<template>
    <footer
        class="border-t border-white/10 bg-zinc-950 py-8 text-center text-sm text-zinc-500"
    >
        <p>© 2026 Hiroshima Live</p>
        <p class="mt-2">広島のライブ情報掲示板</p>

        <nav class="mt-4 flex justify-center gap-5 text-zinc-400">
            <RouterLink to="/terms" class="hover:text-white">
                利用規約
            </RouterLink>

            <RouterLink to="/privacy" class="hover:text-white">
                プライバシーポリシー
            </RouterLink>
        </nav>
    </footer>
</template>

<script setup>
import { RouterLink } from "vue-router";
</script>
