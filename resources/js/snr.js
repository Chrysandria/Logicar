import { SNR } from "./app";
import { createRouter, createWebHistory } from "vue-router";
import traitement from "./component/SNR/traitement.vue";
import today from "./component/SNR/today.vue";
import car from "./component/SNR/car.vue";
import user from "./component/SNR/user.vue";
import reinitPassword from "./component/reinitPassword.vue";


const routes = [
    {
        name: "today",
        component: today,
        path: "/"
    },
    {
        name: "car",
        component: car,
        path: "/car"
    },
    {
        name: "traitement",
        component: traitement,
        path: "/traitement/:date?"
    },
    {
        name: "users",
        component: user,
        path: "/users"
    },
    {
        name: "reinitPassword",
        component: reinitPassword,
        path: "/reinit"
    }
];

export const router = createRouter({
    history: createWebHistory("/snr/"),
    routes
});
SNR.use(router).mount("#app");
