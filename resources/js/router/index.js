import { createRouter, createWebHistory } from "vue-router";

import TopPage from "../pages/TopPage.vue";
import LivesPage from "../pages/LivesPage.vue";
import CalendarPage from "../pages/CalendarPage.vue";
import LiveDetailPage from "../pages/LiveDetailPage.vue";
import LiveCreatePage from "../pages/LiveCreatePage.vue";

const routes = [
    {
        path: "/",
        component: TopPage,
    },
    {
        path: "/lives",
        component: LivesPage,
    },
    {
        path: "/calendar",
        component: CalendarPage,
    },
    {
        path: "/lives/:id",
        component: LiveDetailPage,
    },
    {
        path: "/lives/create",
        name: "live-create",
        component: LiveCreatePage,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    if (!to.meta.requiresAuth) {
        return next();
    }

    try {
        const response = await fetch("/api/user", {
            headers: {
                Accept: "application/json",
            },
            credentials: "include",
        });

        if (response.ok) {
            return next();
        }

        return next("/login");
    } catch (error) {
        return next("/login");
    }
});

export default router;
