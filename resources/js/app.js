import "./bootstrap";
import "../css/app.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { setSeo } from "./utils/seo";
import { removeStructuredData } from "./utils/structuredData";

const app = createApp(App);

router.afterEach((to) => {
    if (!to.path.startsWith("/lives/")) {
        removeStructuredData();
    }

    if (to.path === "/") {
        setSeo({
            title: "広島ライブ情報 | hiroshima-live",
            description:
                "hiroshima-liveは、広島のライブ情報・ライブハウス情報を探せるライブ情報サイトです。",
            url: "https://hiroshima-live.shinji.work/",
            image: "https://hiroshima-live.shinji.work/favicon.png",
            robots: "index, follow",
        });

        return;
    }

    if (to.path === "/lives") {
        setSeo({
            title: "ライブ一覧 | 広島ライブ情報 | hiroshima-live",
            description:
                "広島で開催されるライブ情報・音楽イベント情報を一覧で探せます。",
            url: "https://hiroshima-live.shinji.work/lives",
            image: "https://hiroshima-live.shinji.work/favicon.png",
            robots: "index, follow",
        });

        return;
    }

    if (to.path === "/login") {
        setSeo({
            title: "ログイン | hiroshima-live",
            description: "hiroshima-liveのログインページです。",
            url: "https://hiroshima-live.shinji.work/login",
            image: "https://hiroshima-live.shinji.work/favicon.png",
            robots: "noindex, nofollow",
        });

        return;
    }

    if (to.path === "/register") {
        setSeo({
            title: "新規登録 | hiroshima-live",
            description: "hiroshima-liveの新規登録ページです。",
            url: "https://hiroshima-live.shinji.work/register",
            image: "https://hiroshima-live.shinji.work/favicon.png",
            robots: "noindex, nofollow",
        });

        return;
    }

    if (!to.path.startsWith("/lives/")) {
        setSeo();
    }
});

app.use(router);

app.mount("#app");
