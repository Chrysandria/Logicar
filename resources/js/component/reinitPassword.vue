<template>
        <div class="container-fluid">
            <div class="row d-flex align-items-center justify-content-center">
                <div class="col-12">
                    <h6 class="w-100 text-center mb-4">Souhaitez vous réinitialiser votre mot de passe ?? <br> Veuillez
                        remplir ce formulaire.</h6>
                </div>
                <div class="col-12 col-md-9 col-lg-6 shadow-sm rounded-5 bg-light">
                    <div class="row">
                        <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center">
                            <img src="../../../public/images/logosof.png" alt="Logo sofitrans" class="img-fluid">
                        </div>
                        <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center flex-column p-4">
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
import axios from 'axios';

export default {
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
            try {
                let response = await axios.put('/resetPassword', { name: this.userName, password: this.password, newPassword: this.newPassword });
                Swal.fire({
                    title: "Effectuer",
                    text: response.data.message,
                    icon: "success"
                })

            } catch (error) {
                console.error(error);
                Swal.fire({
                    title: "Oops",
                    text: "Une erreur s'est produite, veuillez recommencer.",
                    icon: "error"
                })
            }
        },

        async getConnectedUser() {
            try {
                const response = await axios.get("/user");
                const user = response.data;
                console.log(user);

                this.userName = user.user.name;

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
