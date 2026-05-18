import { createRouter, createWebHistory } from "vue-router";

import TopPage from "../pages/TopPage.vue";
import LivesPage from "../pages/LivesPage.vue";
import CalendarPage from "../pages/CalendarPage.vue";
import LiveDetailPage from "../pages/LiveDetailPage.vue";
import LiveCreatePage from "../pages/LiveCreatePage.vue";

const routes = [
    { path: "/", component: TopPage },
    { path: "/lives", component: LivesPage },
    { path: "/calendar", component: CalendarPage },
    { path: "/lives/create", component: LiveCreatePage },
    { path: "/lives/:id", component: LiveDetailPage },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
