import "./app";
import "bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import { createRouter, createWebHistory } from "vue-router";
import snr from "./component/snr.vue";


const routes = [
    {
        name: "today",
        component: () => import("./component/SNR/today.vue"),
        path: "/"
    },
    {
        name: "car",
        component: () => import("./component/SNR/car.vue"),
        path: "/car"
    },
    {
        name: "traitement",
        component: () => import("./component/SNR/traitement.vue"),
        path: "/traitement/:date?"
    },
    {
        name: "users",
        component: () => import("./component/SNR/user.vue"),
        path: "/users"
    },
    {
        name: "reinitPassword",
        component: () => import("./component/reinitPassword.vue"),
        path: "/reinit"
    }
];

export const router = createRouter({
    history: createWebHistory("/snr/"),
    routes
});
createApp(snr).use(createPinia()).use(router).mount("#app");
