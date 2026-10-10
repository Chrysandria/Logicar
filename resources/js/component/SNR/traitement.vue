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
            <p class="text-center fw-semibold">Aucun planning Enregistrer pour le moment !!</p>
        </div>

        <div class="card shadow-sm mb-4" v-for="planning in plannings">

            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <div>
                    <h3>Heure de prise de service: {{ planning.HPS.HPS.substring(0, 5) }}</h3>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#traitementModal"
                        @click="setTriParams(planning.HPS.HPS)">Traiter</button>
                </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in planning.emps">
                                <td> {{ emp.matricule }} </td>
                                <td> {{ emp.nom }} </td>
                                <td>{{ emp.service_sigle }}</td>
                                <td> {{ emp.telephone }} </td>
                                <td> {{ emp.lieu_ramassage }} </td>
                                <td> {{ emp.pnt_ramassage_dec }} </td>
                                <td> {{ emp.pnt_ramassage_adm }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="fade modal" tabindex="-1" id="traitementModal" aria-hidden="true" aria-labelledby="#modalTitle">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalTitle">Planning du {{ date }} à {{ hps }}</h5>
                    <button type="button" @click="sub = []" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="planning-treatment-controls mb-4" id="form">
                        <div class="planning-treatment-field">
                            <label for="hps" class="form-label">Heure de début de ramassage</label>
                            <input type="time" name="hps" id="hps" v-model="hdr" class="form-control">
                        </div>
                        <div class="planning-treatment-field">
                            <label for="trajet" class="form-label">Trajet</label>
                            <select name="trajet" id="trajet" class="form-select" v-model="id_trajet">
                                <option value="" class="disabled-value">Choisir un trajet</option>
                                <option v-for="(trajet, i) in trajets" :key="i" :value="trajet.id"> {{ trajet.designation
                                    }} </option>
                            </select>
                        </div>
                        <button class="btn btn-primary planning-treatment-recap" data-bs-toggle="modal"
                            data-bs-target="#recap">
                            <i class="bi bi-list-check me-1" aria-hidden="true"></i>
                            Récapitulation
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped table-bordered text-center align-middle">
                            <thead class="table-dark bg-dark">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Service</th>
                                    <th>Telephone</th>
                                    <th>Lieu de ramassage</th>
                                    <th>Point de ramassage décalée</th>
                                    <th>Point de ramassage ADM</th>
                                    <th>Selection</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="PAT.length === 0">
                                    <td colspan="8" class="text-center fw-semibold"> ✅ Tout est OK !! <br> Veuillez vous
                                        référer à la section recapitulation. </td>
                                </tr>
                                <tr v-for="pat in PAT">
                                    <td> {{ pat.matricule }} </td>
                                    <td> {{ pat.nom }} </td>
                                    <td> {{ pat.service_sigle }} </td>
                                    <td> {{ pat.telephone }} </td>
                                    <td> {{ pat.lieu_ramassage }} </td>
                                    <td> {{ pat.pnt_ramassage_dec }} </td>
                                    <td style="inline-size: 15vw;"> {{ pat.pnt_ramassage_adm }} </td>
                                    <td>
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" :value="pat" v-model="sub">
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button v-if="PAT.length > 0" type="button" class="btn btn-primary"
                        @click="save()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
    <div class="fade modal" tabindex="-1" id="recap" aria-hidden="true" aria-labelledby="#recapTitle">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="recapTitle">Planning du {{ date }} à {{ hps }}</h5>
                    <button type="button" class="btn-close" data-bs-toggle="modal"
                        data-bs-target="#traitementModal"></button>
                </div>
                <div class="modal-body">
                    <div class="card shadow-sm mb-4" v-for="item in details">
                        <div class="card-header bg-primary text-white d-flex justify-content-between"
                            v-for="ram in item.ramassage">
                            <div>
                                <h3>Heure de début de ramassage: {{ ram.HDR.substring(0, 5) }}</h3>
                            </div>
                            <div class="d-flex justify-content-end">
                                <h3>Trajet: {{ ram.trajet }}</h3>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead class="table-dark bg-dark">
                                        <tr>
                                            <th>Matricule</th>
                                            <th>Nom</th>
                                            <th>Service</th>
                                            <th>Telephone</th>
                                            <th>Lieu de ramassage</th>
                                            <th>Point de ramassage ADM</th>
                                            <th>Point de ramassage décalée</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(emp, index) in item.emps" :key="index">
                                            <td> {{ emp.matricule }} </td>
                                            <td> {{ emp.nom }} </td>
                                            <td> {{ emp.service_sigle }} </td>
                                            <td> {{ emp.telephone }} </td>
                                            <td> {{ emp.lieu_ramassage }} </td>
                                            <td> {{ emp.pnt_ramassage_adm }} </td>
                                            <td> {{ emp.pnt_ramassage_dec }} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" :disabled="dsb" @click="generatePdf()">Imprimer</button>
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
    showLoading,
    showRequestError,
} from "../../utils/interface.js";
import { debugLog } from "../../utils/logger.js";

export default {
    data() {
        return {
            date: "",
            service: "",
            plannings: [],
            PAT: [],
            hps: "",
            hdr: "",
            sub: [],
            dsb: true,
            trajets: [],
            id_trajet: "",
            details: [
                {
                    ramassage: [
                        { HDR: "", trajet: "" }
                    ],
                    emps: [
                        { matricule: "", nom: "", service_sigle: "", telephone: "", lieu_ramassage: "", pnt_ramassage_adm: "", pnt_ramassage_dec: "" }
                    ]
                }
            ],
        }
    },

    methods: {
        dateFormatation() {
            this.date = formatFrenchDate(this.$route.params.date);
        },

        async getPlannings() {
            try {
                this.plannings = await fetchPlannings(this.$route.params.date);

            } catch (error) {
                await showRequestError(error, "Impossible de charger les plannings.");
            }
        },

        async setTriParams(h) {
            this.hps = h.substring(0, 5);
            try {
                const params = {
                    date: this.$route.params.date,
                    hps: this.hps
                };
                const [unassigned, ramassages] = await Promise.all([
                    axios.get("/plannings/unassigned", { params }),
                    axios.get("/plannings/ramassages", { params })
                ]);

                this.PAT = unassigned.data;
                this.details = ramassages.data;
                this.dsb = this.PAT.length > 0;
                debugLog("planning-treatment", "Données de traitement chargées.", {
                    availableCount: this.PAT.length,
                    ramassageCount: this.details.length,
                    date: params.date,
                    hps: params.hps,
                });
            } catch (error) {
                debugLog("planning-treatment", "Échec du chargement du traitement.", {
                    status: error.response?.status ?? "network",
                });
                showRequestError(error, "Impossible de charger le traitement du planning.");
            }
        },

        async save() {
            if (!this.hdr || !this.id_trajet || this.sub.length === 0) {
                Swal.fire({
                    title: "Informations manquantes",
                    text: "Indiquez l'heure, le trajet et sélectionnez au moins un employé.",
                    icon: "warning"
                });
                return;
            }

            try {
                await axios.post("/ramassages", {
                    hdr: this.hdr,
                    trip_id: this.id_trajet,
                    planning_ids: this.sub.map((planning) => planning.p_id)
                });
                debugLog("planning-treatment", "Ramassage enregistré.", {
                    employeeCount: this.sub.length,
                    hps: this.hps,
                });

                this.sub = [];
                this.id_trajet = "";
                this.hdr = "";
                await this.setTriParams(this.hps);
            } catch (err) {
                debugLog("planning-treatment", "Échec de l'enregistrement du ramassage.", {
                    status: err.response?.status ?? "network",
                });
                showRequestError(err, "Le ramassage n'a pas pu être enregistré.");
            }
        },

        async getAllTrajet() {
            try {
                let response = await axios.get("/trips");
                this.trajets = response.data;
                debugLog("planning-treatment", "Trajets chargés.", {
                    count: this.trajets.length,
                });
            } catch (err) {
                debugLog("planning-treatment", "Échec du chargement des trajets.", {
                    status: err.response?.status ?? "network",
                });
                showRequestError(err, "Impossible de charger les trajets.");
            }
        },

        async generatePdf() {
            try {
                let response = await axios.post("/plannings/pdf", {
                    date: this.$route.params.date,
                    hps: this.hps,
                    data: this.details
                }, {
                    responseType: 'arraybuffer'
                });

                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                window.open(url);
                debugLog("planning-treatment", "PDF de planning généré.", {
                    date: this.$route.params.date,
                    hps: this.hps,
                    size: blob.size,
                });
            } catch (err) {
                debugLog("planning-treatment", "Échec de la génération du PDF.", {
                    status: err.response?.status ?? "network",
                });
                showRequestError(err, "Le PDF n'a pas pu être généré.");
            }
        }

    },

    async mounted() {
        showLoading();
        try {
            await Promise.all([this.getPlannings(), this.getAllTrajet()]);
        } finally {
            closeLoading();
        }
        this.dateFormatation();
    },

}
</script>
