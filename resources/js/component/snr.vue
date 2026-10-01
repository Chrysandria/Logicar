<template>
    <header class="sticky-top bg-white">
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
                        class="d-none d-lg-flex justify-content-end ms-auto gap-2"
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
                    <div
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
                    </div>
                </div>
            </div>
        </nav>

        <div
            class="d-flex justify-content-between px-4 py-3 bg-light border-bottom mb-4 no-print"
        >
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
import Swal from "sweetalert2";

export default {
    data() {
        return {
            Days: [],
            dateTime: "",
        };
    },

    methods: {
        logOut() {
            Swal.fire({
                title: "Déconnexion",
                text: "Êtes-vous sûr de vouloir vous déconnecter ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Veuilez patienter...",
                        text: "Deconnection en cours",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    try {
                        await axios.post("/logout");
                        window.location.href = "/login";
                    } catch (error) {
                        console.error("Error during logout:", error);
                        Swal.close();
                        Swal.fire({
                            title: "Erreur",
                            text: "Une erreur est survenue lors de la déconnexion. Veuillez réessayer.",
                            icon: "error",
                        });
                    }
                }
            });
        },

        async GetDays() {
            try {
                let response = await axios.get("/nweek");
                this.Days = response.data;
                console.log(this.Days);
            } catch (err) {
                console.error(err);
            }
        },

        CapitaliseFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        async getConnectedUser() {
            try {
                const response = await axios.get("/user");
                const user = response.data;
                console.log(user);

                if (user.user.service_sigle !== "SNR") {
                    let alert = await Swal.fire({
                        title: "Accès refusé pour " + user.user.service_sigle,
                        text: "Vous n'avez pas les droits nécessaires pour accéder à cette éspace.",
                        icon: "error",
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                    window.location.href = "/other_service";
                }
            } catch (error) {
                console.error("Error fetching connected user:", error);
                const alert = await Swal.fire({
                    title: "Erreur",
                    text: "Veuillez vous connecter avant d'accéder à cette éspace.",
                    icon: "error",
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                window.location.href = "/login";
            }
        },
    },

    async mounted() {
        Swal.fire({
            title: "Veuilez patienter...",
            text: "Chargement en cours",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
        await this.getConnectedUser();
        await this.GetDays();
        Swal.close();
        setInterval(() => {
            const now = new Date();
            const options = {
                year: "numeric",
                month: "numeric",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            };
            this.dateTime = now.toLocaleDateString("fr-FR", options);
        }, 1000);
    },
};
</script>
