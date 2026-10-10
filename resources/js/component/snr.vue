<template>
    <header class="workspace-header sticky-top bg-white">
        <nav
            class="navbar navbar-expand-lg navbar-light bg-white pe-4 sticky-top"
        >
            <a href="#" class="navbar-brand">
                <img
                    src="../../../public/images/logosof.png"
                    alt="logo Sofitrans"
                    class="img-fluid"
                    width="300px"
                />
            </a>
            <button
                type="button"
                class="navbar-toggler no-print"
                data-bs-toggle="collapse"
                data-bs-target="#navMenu"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <div class="d-flex flex-column w-100">
                    <div
                        class="ms-auto d-flex flex-wrap mt-2 mt-lg-0 gap-2 align-items-center justify-content-end mb-2 no-print"
                    >
                        <router-link to="/"
                            ><button class="btn btn-primary">
                                Aujourd'hui
                            </button></router-link
                        >

                        <router-link
                            v-for="day in Days"
                            :to="{
                                name: 'traitement',
                                params: { date: day.date },
                            }"
                        >
                            <button class="btn btn-primary">
                                {{ CapitaliseFirst(day.jour) }}
                            </button>
                        </router-link>
                        <router-link to="/car"
                            ><button class="btn btn-secondary">
                                Gestion des cars
                            </button></router-link
                        >
                    </div>
                    <div
                        class="d-flex justify-content-end ms-auto gap-2"
                    >
                        <router-link to="/reinit"
                            ><button class="btn btn-secondary">
                                Paramètre de compte
                            </button></router-link
                        >
                        <router-link to="/users"
                            ><button class="btn btn-secondary">
                                Gestion des utilisateurs
                            </button></router-link
                        >
                        <button class="btn btn-danger" @click="logOut">
                            Déconnexion
                        </button>
                    </div>
                    <!-- <div
                        class="d-flex d-lg-none justify-content-end ms-auto gap-2"
                    >
                        <router-link to="/reinit"
                            ><button
                                class="btn btn-secondary"
                                title="Paramètre de compte"
                            >
                                <i class="bi bi-person-circle"></i></button
                        ></router-link>
                        <router-link to="/users"
                            ><button class="btn btn-secondary" title="Gestion des utilisateurs">
                                <i class="bi bi-people"></i></button
                        ></router-link>
                        <button class="btn btn-danger" @click="logOut" title="Déconnexion">
                            <i class="bi bi-box-arrow-left"></i>
                        </button>
                    </div> -->
                </div>
            </div>
        </nav>

        <div class="workspace-topbar d-flex justify-content-between px-4 py-3 bg-light border-bottom no-print">
            <div>
                <h5>Espace SNR admin</h5>
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

                if (user.service_sigle !== "SNR") {
                    await showAccessDenied(user.service_sigle);
                    window.location.href = "/other_service";
                }
            } catch {
                await showAuthenticationRequired();
                window.location.href = "/login";
            }
        },
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
