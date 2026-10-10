import "./app";
import { createApp } from "vue";
import { createPinia } from "pinia";
import login from "./component/login.vue";

createApp(login).use(createPinia()).mount("#app");
