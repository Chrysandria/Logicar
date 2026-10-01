<template>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <div>
                <h4 class="text-decoration-underline">Gestion des utilisateurs</h4>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newUser">Nouvel
                    utilisateur</button>
            </div>
        </div>
        <div class="table-responsive">
            <div class="mb-4">
                <input type="search" name="" id="" class="form-control" placeholder="Nom d'utilisateur ou service"
                    @input="search">
            </div>
            <table class="table table-striped table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Id</th>
                        <th>Nom d'utilisateur</th>
                        <th>Service</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td> {{ user.id }} </td>
                        <td> {{ user.name }} </td>
                        <td> {{ user.service_sigle }} </td>
                        <td> <button class="btn btn-danger" @click="deleteUser(user.id)"><i class="bi bi-trash"></i>
                                Supprimer</button> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="fade modal" tabindex="-1" id="newUser" aria-hidden="true" aria-labelledby="#modalTitle">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white" id="modalTitle">Ajouter un nouvel utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nom d'utilisateur:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-primary"></i></span>
                            <input type="text" class="form-control" v-model="name">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Service:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-building text-primary"></i></span>
                            <select class="form-select" v-model="service">
                                <option disabled value="">Sélectionner un service</option>
                                <option v-for="srv in services" :value="srv.service_sigle">
                                    {{ srv.service_sigle }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <p class="fw-semibold text-center text-danger"> {{ error_message }} </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="register()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    data() {
        return {
            users: [],
            services: [],
            service: "",
            name: "",
            error_message: ""
        }
    },

    methods: {
        async fetchUser() {
            try {
                let response = await axios.get("/allUsers");
                this.users = response.data;

                console.log(this.users);

            } catch (err) {
                console.error(err);
            }
        },

        async fetchServices() {
            try {
                let response = await axios.get("/getServices");
                this.services = response.data;
                console.log(this.services);

            } catch (err) {
                console.error(err);

            }
        },

        async register() {
            if (this.name === "" || this.service === "") {
                this.error_message = "Veuillez entrer un nom d'utilisateur puis choisir un service";

                return;
            }

            try {
                let response = await axios.post("/register", {
                    name: this.name,
                    service: this.service
                });

                Swal.fire({
                    Title: "Effectuer",
                    icon: "success",
                    text: response.data.message
                });

                this.name = "",
                this.service = ""
                this.fetchUser();
            } catch (err) {
                console.error(err);
                Swal.fire({
                    Title: "Erreur",
                    icon: "error",
                    text: "Une erreur s'est produite, veuillez reessayer."
                })
            }
        },

        async deleteUser(id) {
            try {
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `Voulez-vous rellemment supprimer cet utilisateur ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then(async (result) => {
                    if (result.isConfirmed) {

                        let response = await axios.delete("/deleteUser", {
                            params: {
                                id: id
                            }
                        });

                        Swal.fire({
                            title: "Effectuer",
                            icon: "success",
                            text: response.data.message
                        });

                        this.fetchUser();
                    }
                });
            } catch (err) {
                console.error(err);
                Swal.fire({
                    Title: "Erreur",
                    icon: "error",
                    text: "Une erreur s'est produite, veuillez reessayer."
                })
            }
        }
    },

    async mounted() {
        await this.fetchServices();
        await this.fetchUser();
    }
}
</script>
