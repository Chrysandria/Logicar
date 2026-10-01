import { OS } from "./app";
import { createRouter, createWebHistory } from "vue-router";
import saisie from "./component/OS/saisie.vue";
import today from "./component/OS/today.vue";
import reinitPassword from "./component/reinitPassword.vue";

const routes = [
    {
        name: "today",
        component: today,
        path: "/",
    },
    {
        name: "saisie",
        component: saisie,
        path: "/saisie/:date?/:service?",
    },
    {
        name: "reinitPassword",
        component: reinitPassword,
        path: "/reinit",
    },
];

export const router = createRouter({
    history: createWebHistory("/other_service/"),
    routes,
});
OS.use(router).mount("#app");
