<template>
    <div class="container-fluid">
        <header class="planning-toolbar mb-4">
            <div class="planning-toolbar__content">
                <span class="planning-toolbar__icon" aria-hidden="true">
                    <i class="bi bi-calendar3"></i>
                </span>
                <div>
                    <p class="planning-toolbar__label">Planning du</p>
                    <h1 class="planning-toolbar__date">{{ date }}</h1>
                </div>
            </div>

            <div class="planning-toolbar__actions">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#search">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>
                    Rechercher
                </button>
            </div>
        </header>

        <div v-if="plannings.length == 0"
            class="w-100 flex-fill d-flex gap-4 flex-column align-items-center justify-content-center mt-5 pt-5"
            style="opacity: 0.8;">
            <h1 class="text-center">❌</h1>
            <p class="text-center fw-semibold">Aucun planning Enregistrer pour le moment !!</p>
        </div>

        <div class="card shadow-sm mb-4" v-for="planning in plannings">
            <div class="card-header bg-primary text-white">
                <h3>Heure de prise de service: {{ planning.HPS.HPS.substring(0, 5) }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-stripped text-center">
                        <thead class="bg-primary table-primary">
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Telephone</th>
                                <th>Lieu de ramassage</th>
                                <th>Point de ramassage décalée</th>
                                <th>Point de ramassage ADM</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in planning.emps">
                                <td> {{ emp.matricule }} </td>
                                <td> {{ emp.nom }} </td>
                                <td> {{ emp.telephone }} </td>
                                <td> {{ emp.lieu_ramassage }} </td>
                                <td> {{ emp.pnt_ramassage_dec }} </td>
                                <td> {{ emp.pnt_ramassage_adm }} </td>
                                <td v-if="!emp.ok" class="text-danger fw-semibold"> Non récupérer </td>
                                <td v-else></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="fade modal" tabindex="-1" id="search">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Effectuer une recherche pour une date.</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        @click="reset"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div>
                            <label for="date" class="form-label fw-semibold">Entrer une date :</label>
                            <div class="d-flex gap-2">
                                <input type="date" name="date" id="date" class="form-control" v-model="filter">
                                <button class="btn btn-primary" @click="filterPlannings">Rechercher</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="results.length === 0 && message" class="text-center fw-semibold mt-5">
                        <h1>🔍</h1>
                        <p>{{ message }}</p>
                    </div>

                    <div v-else>
                        <div class="card shadow-sm mb-4" v-for="planning in results">
                            <div class="card-header bg-primary text-white">
                                <h3>Heure de prise de service: {{ planning.HPS.HPS.substring(0, 5) }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-stripped text-center">
                                        <thead class="bg-primary table-primary">
                                            <tr>
                                                <th>Matricule</th>
                                                <th>Nom</th>
                                                <th>Telephone</th>
                                                <th>Lieu de ramassage</th>
                                                <th>Point de ramassage décalée</th>
                                                <th>Point de ramassage ADM</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="emp in planning.emps">
                                                <td> {{ emp.matricule }} </td>
                                                <td> {{ emp.nom }} </td>
                                                <td> {{ emp.telephone }} </td>
                                                <td> {{ emp.lieu_ramassage }} </td>
                                                <td> {{ emp.pnt_ramassage_dec }} </td>
                                                <td> {{ emp.pnt_ramassage_adm }} </td>
                                                <td v-if="!emp.ok" class="text-danger fw-semibold"> Non récupérer </td>
                                                <td v-else></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    closeLoading,
    fetchPlannings,
    formatFrenchDate,
    getLocalIsoDate,
    showLoading,
    showRequestError,
} from "../../utils/interface.js";
import { useSessionStore } from "../../stores/session.js";

export default {
    setup() {
        return { session: useSessionStore() };
    },

    data() {
        return {
            date: "",
            filter: "",
            plannings: [],
            results: [],
            message: ""
        }
    },

    methods: {
        dateFormatation() {
            this.date = formatFrenchDate(new Date());
        },

        async getPlannings() {
            try {
                this.plannings = await fetchPlannings(
                    getLocalIsoDate(),
                    this.session.service,
                );

            } catch (error) {
                await showRequestError(error, "Impossible de charger les plannings.");
            }
        },

        async filterPlannings() {
            showLoading("Recherche en cours");
            try {
                const plannings = await fetchPlannings(
                    this.filter,
                    this.session.service,
                );

                if (plannings.length === 0) {
                    this.results = [];
                    this.message = "Aucun résultat trouvé pour cette date.";
                } else {
                    this.results = plannings;
                    this.message = "";
                }
            } catch {
                this.message = "La recherche a échoué. Veuillez réessayer.";
            } finally {
                closeLoading();
            }
        },

        reset() {
            this.filter = "";
            this.results = [];
        }

    },

    async mounted() {
        showLoading();
        try {
            await this.getPlannings();
        } finally {
            closeLoading();
        }
        this.dateFormatation();
    },

}
</script>
