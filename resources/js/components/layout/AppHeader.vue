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
            </RouterLink>

            <nav class="hidden gap-6 text-sm text-zinc-300 md:flex">
                <RouterLink to="/" class="hover:text-white">トップ</RouterLink>
                <RouterLink to="/lives" class="hover:text-white"
                    >新着ライブ</RouterLink
                >
                <RouterLink to="/calendar" class="hover:text-white"
                    >カレンダー</RouterLink
                >
                <a href="/login" class="hover:text-white">ログイン</a>
            </nav>
            <RouterLink
                to="/lives/create"
                class="rounded-full bg-red-500 px-5 py-2 text-sm font-bold text-white hover:bg-red-600"
            >
                ライブ投稿
            </RouterLink>
        </div>
    </header>
</template>
