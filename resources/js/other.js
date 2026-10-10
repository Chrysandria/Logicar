import "./app";
import "bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import { createRouter, createWebHistory } from "vue-router";
import other_service from "./component/other_service.vue";

const routes = [
    {
        name: "today",
        component: () => import("./component/OS/today.vue"),
        path: "/",
    },
    {
        name: "saisie",
        component: () => import("./component/OS/saisie.vue"),
        path: "/saisie/:date?/:service?",
    },
    {
        name: "reinitPassword",
        component: () => import("./component/reinitPassword.vue"),
        path: "/reinit",
    },
];

export const router = createRouter({
    history: createWebHistory("/other_service/"),
    routes,
});
createApp(other_service).use(createPinia()).use(router).mount("#app");
