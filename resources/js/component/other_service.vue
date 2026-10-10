<template>
    <header class="workspace-header sticky-top bg-white">
        <nav class="navbar navbar-expand-lg navbar-light bg-white pe-4 sticky-top">
            <a href="#" class="navbar-brand">
                <img src="../../../public/images/logosof.png" alt="logo Sofitrans" class="img-fluid" width="300px">
            </a>
            <button type="button" class="navbar-toggler " data-bs-toggle="collapse" data-bs-target="#navMenu"><span
                    class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navMenu">
                <div class="d-flex flex-column align-items-end gap-1 w-100">
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
                        <router-link to="/"><button class="btn btn-primary">Aujourd'hui</button></router-link>

                        <router-link v-for="day in Days"
                            :to="{ name: 'saisie', params: { date: day.date, service: service } }">
                            <button class="btn btn-primary">{{ CapitaliseFirst(day.jour) }}</button>
                        </router-link>
                    </div>
                    <div class="workspace-nav-actions d-flex align-items-center justify-content-end gap-2">
                        <router-link to="/reinit"><button class="btn btn-secondary">Paramètre de
                                compte</button></router-link>
                        <button class="btn btn-danger" @click="logOut">Déconnexion</button>
                    </div>
                </div>

            </div>
        </nav>
        <div class="workspace-topbar d-flex justify-content-between px-4 py-3 bg-light border-bottom">
            <div>
                <h5>Service: {{ service }} </h5>
            </div>
            <div>
                <p>{{ dateTime }}</p>
            </div>
        </div>
    </header>

    <main>
        <router-view v-slot="{ Component, route }">
            <keep-alive>
                <component :is="Component" :key="route.params.date" />
            </keep-alive>
        </router-view>
    </main>
</template>

<script>
import {
    closeLoading,
    getPlanningCalendar,
    logout,
    showAccessDenied,
    showAuthenticationRequired,
    showLoading,
    startFrenchClock,
} from "../utils/interface.js";
import { useSessionStore } from "../stores/session.js";

export default {
    setup() {
        return { session: useSessionStore() };
    },

    data() {
        return {
            Days: getPlanningCalendar(),
            dateTime: "",
            service: "",
            clockInterval: null,
        };
    },

    methods: {
        logOut() {
            return logout(this.session);
        },

        CapitaliseFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        async getConnectedUser() {
            try {
                const user = await this.session.fetchSession();

                if (user.service_sigle === "SNR") {
                    await showAccessDenied(user.service_sigle);
                    window.location.href = "/snr"
                } else {
                    this.service = user.service_sigle;
                }

            } catch {
                await showAuthenticationRequired();
                window.location.href = "/login";

            }

        }
    },

    async mounted() {
        showLoading();
        try {
            await this.getConnectedUser();
        } finally {
            closeLoading();
        }
        this.clockInterval = startFrenchClock((dateTime) => {
            this.dateTime = dateTime;
        });
    },

    beforeUnmount() {
        window.clearInterval(this.clockInterval);
    },
};
</script>
