import { createRouter, createWebHistory } from "vue-router";

import TopPage from "../pages/TopPage.vue";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import LivesPage from "../pages/LivesPage.vue";
import LiveCreatePage from "../pages/LiveCreatePage.vue";
import LiveDetailPage from "../pages/LiveDetailPage.vue";
import CalendarPage from "../pages/CalendarPage.vue";
import ForgotPassword from "../pages/ForgotPassword.vue";
import ResetPassword from "../pages/ResetPassword.vue";

const routes = [
    {
        path: "/",
        name: "home",
        component: TopPage,
    },
    {
        path: "/login",
        name: "login",
        component: Login,
    },
    {
        path: "/register",
        name: "register",
        component: Register,
    },
    {
        path: "/forgot-password",
        name: "password.forgot",
        component: ForgotPassword,
    },
    {
        path: "/reset-password",
        name: "password.reset",
        component: ResetPassword,
    },
    {
        path: "/lives",
        name: "lives.index",
        component: LivesPage,
    },
    {
        path: "/lives/create",
        name: "lives.create",
        component: LiveCreatePage,
    },
    {
        path: "/lives/:id",
        name: "lives.show",
        component: LiveDetailPage,
    },
    {
        path: "/calendar",
        name: "calendar",
        component: CalendarPage,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
