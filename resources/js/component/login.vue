<template>
    <div class="auth-screen d-flex align-items-center justify-content-center min-vh-100">
        <div class="container-fluid">
            <div class="row d-flex align-items-center justify-content-center p-4">
                <div class="col-12">
                    <h2 class="auth-heading w-100 text-center mb-4">Bienvenue sur Logicar 👋🏽</h2>
                </div>
                <div class="auth-card col-12 col-md-9 col-lg-10 shadow-sm rounded-5 bg-white bg-gradient">
                    <div class="row">
                        <div class="auth-brand col-12 col-lg-6 d-flex align-items-center justify-content-center">
                            <img src="../../../public/images/logosof.png" alt="" class="img-fluid">
                        </div>
                        <div class="auth-form col-12 col-lg-6 d-flex align-items-center justify-content-center flex-column p-4">
                            <h3 class="text-decoration-underline">Connexion</h3>
                            <form class="w-100 p-4 d-flex flex-column" @submit.prevent="submitLogin">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Nom d'utilisateur:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-person text-primary"></i></span>
                                        <input type="text" class="form-control" v-model="name" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Mot de passe:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-lock text-primary"></i></span>
                                        <input :type="password_input" class="form-control" v-model="password" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <input type="checkbox" name="show_password" id="show_password"
                                        class="form-check-input"
                                        @click="password_input = (password_input === 'password') ? 'text' : 'password'">
                                    <label for="show_password" class="form-label fw-semibold ms-2">Afficher le mot de
                                        passe</label>
                                </div>
                                <button type="submit" class="btn btn-primary mb-4">Se connecter</button>
                                <div class="d-flex align-items-end justify-content-end">
                                    <a @click="NoAccount" class="link-success btn text-decoration-underline">Pas encore
                                        de compte ?</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useSessionStore } from "../stores/session.js";
import { showLoading } from "../utils/interface.js";

export default {
    setup() {
        return { session: useSessionStore() };
    },

    data() {
        return {
            name: "",
            password: "",
            password_input: "password"
        }
    },

    methods: {
        NoAccount() {
            Swal.fire({
                title: "Pas encore de compte ?",
                text: "Veuillez contacter le SNR.",
                icon: "info"
            })
        },

        async submitLogin() {
            showLoading("Traitement en cours");
            try {
                const user = await this.session.login({
                    name: this.name,
                    password: this.password,
                });
                if (user.service_sigle === "SNR") {
                    window.location.href = "/snr"
                } else {
                    window.location.href = "/other_service"
                }
            } catch (error) {
                console.error('Error during login:', error);
                Swal.fire({
                    title: "Oops",
                    text: "Information de connexion incorrecte. Veuillez réessayer.",
                    icon: "error"
                })
            }
        },

        async getConnectedUser() {
            try {
                const user = await this.session.fetchSession();

                if (user.service_sigle === "SNR") {
                    window.location.href = "/snr"
                } else {
                    window.location.href = "/other_service"
                }

            } catch (error) {
                console.error("Error fetching connected user:", error);
            }

        },

    },

    async mounted() {
        await this.getConnectedUser();
    }
}

</script>
