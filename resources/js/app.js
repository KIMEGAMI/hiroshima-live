import "./bootstrap";
import "../css/app.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { DEFAULT_IMAGE, SITE_URL, setSeo } from "./utils/seo";
import { removeStructuredData } from "./utils/structuredData";

const app = createApp(App);

const privateRoutes = new Map([
    [
        "/login",
        {
            title: "ログイン | hiroshima-live",
            description: "hiroshima-liveのログインページです。",
        },
    ],
    [
        "/register",
        {
            title: "新規登録 | hiroshima-live",
            description: "hiroshima-liveの新規登録ページです。",
        },
    ],
    [
        "/forgot-password",
        {
            title: "パスワード再設定 | hiroshima-live",
            description: "hiroshima-liveのパスワード再設定ページです。",
        },
    ],
    [
        "/reset-password",
        {
            title: "パスワード再設定 | hiroshima-live",
            description: "hiroshima-liveのパスワード再設定ページです。",
        },
    ],
]);

router.afterEach((to) => {
    if (!to.path.startsWith("/lives/")) {
        removeStructuredData();
    }

    if (to.path === "/") {
        setSeo({
            title: "広島ライブ情報 | hiroshima-live",
            description:
                "hiroshima-liveは、広島のライブ情報・ライブハウス情報を探せるライブ情報サイトです。",
            url: `${SITE_URL}/`,
            image: DEFAULT_IMAGE,
        });

        return;
    }

    if (to.path === "/lives") {
        setSeo({
            title: "ライブ一覧 | 広島ライブ情報 | hiroshima-live",
            description:
                "広島で開催されるライブ情報・音楽イベント情報を一覧で探せます。",
            url: `${SITE_URL}/lives`,
            image: DEFAULT_IMAGE,
        });

        return;
    }

    if (privateRoutes.has(to.path)) {
        const seo = privateRoutes.get(to.path);

        setSeo({
            ...seo,
            url: `${SITE_URL}${to.path}`,
            image: DEFAULT_IMAGE,
            robots: "noindex, nofollow",
        });

        return;
    }

    if (!to.path.startsWith("/lives/")) {
        setSeo({
            url: `${SITE_URL}${to.path}`,
        });
    }
});

app.use(router);

app.mount("#app");
