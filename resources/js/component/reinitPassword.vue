<template>
        <div class="auth-screen auth-screen--fit container-fluid d-flex align-items-center justify-content-center">
            <div class="row d-flex align-items-center justify-content-center">
                <div class="col-12">
                    <h6 class="auth-heading w-100 text-center mb-4">Souhaitez vous réinitialiser votre mot de passe ?? <br> Veuillez
                        remplir ce formulaire.</h6>
                </div>
                <div class="auth-card auth-card--fit col-12 col-md-9 col-lg-10 shadow-sm rounded-5 bg-light">
                    <div class="row">
                        <div class="auth-brand col-12 col-lg-5 d-flex align-items-center justify-content-center">
                            <img src="../../../public/images/logosof.png" alt="Logo sofitrans" class="img-fluid">
                        </div>
                        <div class="auth-form col-12 col-lg-7 d-flex align-items-center justify-content-center flex-column p-4">
                            <h3 class="text-decoration-underline">Reinitialisation du mot de passe</h3>
                            <form class="w-100 p-4 d-flex flex-column" @submit.prevent="resetPassword">
                                <div class="mb-4">
                                    <p class="fw-semibold">Nom d'utilisateur: {{ userName }}</p>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Ancien mot de passe:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-lock text-primary"></i></span>
                                        <input :type="password_input" class="form-control" v-model="password" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Nouveau mot de passe:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-lock text-primary"></i></span>
                                        <input :type="password_input" class="form-control" v-model="newPassword" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Confirmer le nouveau mot de passe:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-lock text-primary"></i></span>
                                        <input :type="password_input" class="form-control" v-model="confirm" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <input type="checkbox" name="show_password" id="show_password"
                                        class="form-check-input"
                                        @click="password_input = (password_input === 'password') ? 'text' : 'password'">
                                    <label for="show_password" class="form-label fw-semibold ms-2">Afficher les mots de
                                        passe</label>
                                </div>
                                <button type="submit" class="btn btn-primary mb-4">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
import { useSessionStore } from "../stores/session.js";

export default {
    setup() {
        return { session: useSessionStore() };
    },

    data() {
        return {
            userName: "",
            password: "",
            newPassword: "",
            confirm: "",
            password_input: "password"
        }
    },

    methods: {
        async resetPassword() {
            if (this.newPassword !== this.confirm) {
                Swal.fire({
                    title: "Mot de passe invalide",
                    text: "Les deux nouveaux mots de passe ne correspondent pas.",
                    icon: "error"
                });
                return;
            }

            try {
                const response = await this.session.updatePassword({
                    current_password: this.password,
                    password: this.newPassword
                });
                Swal.fire({
                    title: "Effectuer",
                    text: response.data.message,
                    icon: "success"
                })
                this.password = "";
                this.newPassword = "";
                this.confirm = "";

            } catch (error) {
                Swal.fire({
                    title: "Oops",
                    text: error.response?.data?.message || "Une erreur s'est produite, veuillez recommencer.",
                    icon: "error"
                })
            }
        },

        async getConnectedUser() {
            try {
                const user = await this.session.fetchSession();
                this.userName = user.name;

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
