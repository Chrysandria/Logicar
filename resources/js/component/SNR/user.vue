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
                <input
                    type="search"
                    class="form-control"
                    placeholder="Rechercher par nom d'utilisateur ou service"
                    aria-label="Rechercher un utilisateur par nom ou service"
                    v-model="searchTerm"
                >
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
                    <tr v-for="user in filteredUsers" :key="user.id">
                        <td> {{ user.id }} </td>
                        <td> {{ user.name }} </td>
                        <td> {{ user.service_sigle }} </td>
                        <td> <button class="btn btn-danger" @click="deleteUser(user.id)"><i class="bi bi-trash"></i>
                                Supprimer</button> </td>
                    </tr>
                    <tr v-if="filteredUsers.length === 0">
                        <td colspan="4" class="py-4 text-muted">Aucun utilisateur ne correspond à la recherche.</td>
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
import { showRequestError } from "../../utils/interface.js";
import { debugLog } from "../../utils/logger.js";

export default {
    data() {
        return {
            users: [],
            searchTerm: "",
            services: [],
            service: "",
            name: "",
            error_message: ""
        }
    },

    computed: {
        filteredUsers() {
            const query = this.searchTerm.trim().toLocaleLowerCase("fr-FR");

            if (!query) {
                return this.users;
            }

            return this.users.filter((user) =>
                [user.name, user.service_sigle]
                    .some((value) => value.toLocaleLowerCase("fr-FR").includes(query)),
            );
        },
    },

    methods: {
        async fetchUser() {
            try {
                let response = await axios.get("/users");
                this.users = response.data;
                debugLog("users", "Utilisateurs chargés.", { count: this.users.length });
            } catch (error) {
                debugLog("users", "Échec du chargement des utilisateurs.", {
                    status: error.response?.status ?? "network",
                });
                showRequestError(error, "Impossible de charger les utilisateurs.");
            }
        },

        async fetchServices() {
            try {
                let response = await axios.get("/services");
                this.services = response.data;
                debugLog("users", "Services chargés.", { count: this.services.length });

            } catch (error) {
                debugLog("users", "Échec du chargement des services.", {
                    status: error.response?.status ?? "network",
                });
                showRequestError(error, "Impossible de charger les services.");
            }
        },

        async register() {
            if (this.name === "" || this.service === "") {
                this.error_message = "Veuillez entrer un nom d'utilisateur puis choisir un service";

                return;
            }

            try {
                let response = await axios.post("/users", {
                    name: this.name,
                    service_sigle: this.service
                });
                debugLog("users", "Utilisateur créé.", { service: this.service });

                Swal.fire({
                    title: "Effectuer",
                    icon: "success",
                    text: response.data.message
                });

                this.name = "";
                this.service = "";
                await this.fetchUser();
            } catch (err) {
                showRequestError(err, "Une erreur s'est produite, veuillez réessayer.");
            }
        },

        async deleteUser(id) {
            try {
                const confirmation = await Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `Voulez-vous rellemment supprimer cet utilisateur ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                });

                if (!confirmation.isConfirmed) return;

                await axios.delete(`/users/${id}`);
                debugLog("users", "Utilisateur supprimé.", { userId: id });

                await Swal.fire({
                    title: "Effectuer",
                    icon: "success",
                    text: "Utilisateur supprimé.",
                });

                await this.fetchUser();
            } catch (err) {
                showRequestError(err, "Une erreur s'est produite, veuillez réessayer.");
            }
        }
    },

    async mounted() {
        await Promise.all([this.fetchServices(), this.fetchUser()]);
    }
}
</script>
