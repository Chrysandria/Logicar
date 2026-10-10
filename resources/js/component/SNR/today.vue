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
        </header>

        <div v-if="plannings.length == 0"
            class="w-100 flex-fill d-flex gap-4 flex-column align-items-center justify-content-center mt-5 pt-5"
            style="opacity: 0.8;">
            <h1 class="text-center">❌</h1>
            <p class="text-center fw-semibold">Aucun planning Enregistrer pour Aujourd'hui !!</p>
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
                                <th>Service</th>
                                <th>Telephone</th>
                                <th>Lieu de ramassage</th>
                                <th>Point de ramassage décalée</th>
                                <th>Point de ramassage ADM</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in planning.emps">
                                <td> {{ emp.matricule }} </td>
                                <td> {{ emp.nom }} </td>
                                <td> {{ emp.service_sigle }} </td>
                                <td> {{ emp.telephone }} </td>
                                <td> {{ emp.lieu_ramassage }} </td>
                                <td> {{ emp.pnt_ramassage_dec }} </td>
                                <td> {{ emp.pnt_ramassage_adm }} </td>
                                <td v-if="!emp.ok" class="text-danger fw-semibold"> Non récupérer </td>
                                <td v-else><button class="btn btn-dark" @click="set_non_recups(emp.p_id)">Signaler</button></td>
                            </tr>
                        </tbody>
                    </table>
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

export default {
    data() {
        return {
            date: "",
            filter: "",
            plannings: [],
            results: [],
        }
    },

    methods: {
        dateFormatation() {
            this.date = formatFrenchDate(new Date());
        },

        async getPlannings() {
            try {
                this.plannings = await fetchPlannings(getLocalIsoDate());

            } catch (error) {
                console.error("Impossible de charger les plannings.", error);
            }
        },

        async set_non_recups(id){
            try {
                await axios.patch(`/plannings/${id}`, {
                    ok: false
                });

                await this.getPlannings();

            } catch (error) {
                showRequestError(error, "Impossible de mettre à jour le planning.");
            }
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
