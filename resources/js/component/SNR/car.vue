<template>
    <div class="container-fluid">
        <div class="page-heading">
            <div class="page-heading__title">
                <span class="page-heading__icon" aria-hidden="true">
                    <i class="bi bi-truck-front"></i>
                </span>
                <div>
                    <h2>Gestion des cars</h2>
                    <p>Suivez la flotte, les mouvements et les anomalies.</p>
                </div>
            </div>
            <div class="page-heading__actions">
                <button
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#dmModal"
                >
                    <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>
                    Mouvement du jour
                </button>
                <button
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#carModal"
                >
                    <i class="bi bi-list-ul me-1" aria-hidden="true"></i>
                    Liste des cars
                </button>
                <button
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#anomaliesModal"
                >
                    <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                    Liste des anomalies
                </button>
            </div>
        </div>
        <BarChart :key="i" />
    </div>
    <div
        class="fade modal"
        tabindex="-1"
        id="carModal"
        aria-hidden="true"
        aria-labelledby="#modalTitle"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle">
                        Gestion des cars.
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        @click="fetchCars()"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary" @click="newCar()">
                            Nouveau car
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-stripped table-bordered text-center align-middle"
                        >
                            <thead class="table-secondary">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="car in cars" :key="car.id">
                                    <td v-if="car.nouveau">
                                        <input
                                            type="text"
                                            v-model="car.matricule"
                                            class="form-control"
                                            placeholder="Matricule du nouveau car"
                                        />
                                    </td>
                                    <td v-else>{{ car.matricule }}</td>
                                    <td
                                        v-if="car.disponibilite"
                                        class="fw-semibold"
                                    >
                                        <button
                                            class="btn btn-dark"
                                            @click="startAnomaly(car.matricule)"
                                        >
                                            Déclarer une anomalie
                                        </button>
                                    </td>
                                    <td v-else-if="car.nouveau">
                                        <button
                                            class="btn btn-success"
                                            @click="saveNewCar(car.matricule)"
                                        >
                                            Enregistrer
                                        </button>
                                    </td>
                                    <td v-else class="fw-semibold text-danger">
                                        Hors service
                                    </td>
                                    <td>
                                        <button
                                            v-if="!car.nouveau"
                                            class="btn btn-danger"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            :title="
                                                'Supprimer le car ' +
                                                car.matricule
                                            "
                                            @click="deleteCar(car.matricule)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <button
                                            class="btn btn-secondary"
                                            v-else
                                            @click="fetchCars()"
                                        >
                                            Annuler
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="fade modal"
        tabindex="-1"
        id="dmModal"
        aria-hidden="true"
        aria-labelledby="#dmTitle"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="dmTitle">Mouvement du jour.</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        @click="fetchDailyMoov"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table
                            class="table table-stripped table-bordered text-center align-middle"
                        >
                            <thead class="table-secondary">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Distance parcourue (km)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="moov in daily_moov"
                                    :key="moov.matricule"
                                >
                                    <td>{{ moov.matricule }}</td>
                                    <td>
                                        <input
                                            type="number"
                                            v-model="moov.distance_parcourue"
                                            class="form-control"
                                        />
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-success"
                                            v-if="moov.km === 0"
                                            @click="
                                                saveDailyMoov(
                                                    moov.matricule,
                                                    moov.distance_parcourue,
                                                    moov.km,
                                                )
                                            "
                                        >
                                            Enregistrer
                                        </button>
                                        <button
                                            class="btn btn-warning"
                                            v-else
                                            @click="
                                                saveDailyMoov(
                                                    moov.matricule,
                                                    moov.distance_parcourue,
                                                    moov.km,
                                                )
                                            "
                                        >
                                            Modifier
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="fade modal"
        tabindex="-1"
        id="anomaliesModal"
        aria-hidden="true"
        aria-labelledby="#anomaliesTitle"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="dmTitle">
                        Anomalies récentes.
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        @click="fetchCurrentAnomalies"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="filter" class="form-label fw-semibold"
                            >Effectuer une recherche:</label
                        >
                        <div class="row">
                            <div class="w-50">
                                <select
                                    name="filter"
                                    id="filter"
                                    class="form-select"
                                    v-model="filter"
                                    @change="findAnom()"
                                >
                                    <option value="" disabled>
                                        Choisir un matricule
                                    </option>
                                    <option
                                        v-for="car in cars"
                                        :value="car.matricule"
                                    >
                                        {{ car.matricule }}
                                    </option>
                                </select>
                            </div>
                            <div class="col" :style="resetdisplay">
                                <button class="btn btn-light" @click="reset">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-stripped table-bordered text-center align-middle"
                        >
                            <thead class="table-secondary">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Catégorie</th>
                                    <th>Date de début</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="current_anomalies.length === 0">
                                    <td
                                        class="text-center align-middle fw-semibold"
                                        colspan="4"
                                        style="opacity: 0.8"
                                    >
                                        <h3>❌</h3>
                                        Aucun résultat trouvé.
                                    </td>
                                </tr>
                                <tr
                                    v-for="anomalie in current_anomalies"
                                    :key="anomalie.id"
                                    v-else
                                >
                                    <td>{{ anomalie.mtrl_car }}</td>
                                    <td>{{ anomalie.description }}</td>
                                    <td>
                                        {{ dateFormatation(anomalie.debut) }}
                                    </td>
                                    <td v-if="!anomalie.fin">
                                        <button
                                            class="btn btn-secondary"
                                            @click="
                                                EndAnomaly(
                                                    anomalie.id,
                                                    anomalie.mtrl_car,
                                                )
                                            "
                                        >
                                            Reprise de service
                                        </button>
                                    </td>
                                    <td v-else class="fw-semibold">
                                        Date de fin:
                                        {{ dateFormatation(anomalie.fin) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
import BarChart from "./chart.vue";
import axios from "axios";
import {
    formatFrenchDate,
    initTooltips,
    showRequestError,
} from "../../utils/interface.js";
import { debugLog } from "../../utils/logger.js";
export default {
    components: {
        BarChart,
    },

    data() {
        return {
            cars: [],
            daily_moov: [],
            i: 0,
            current_anomalies: [],
            filter: "",
            resetdisplay: "display: none",
        };
    },

    methods: {
        async fetchCars() {
            try {
                const response = await axios.get("/cars");
                this.cars = response.data;
                debugLog("fleet", "Cars chargés.", { count: this.cars.length });
            } catch (error) {
                debugLog("fleet", "Échec du chargement des cars.", {
                    status: error.response?.status ?? "network",
                });
                this.showError(error, "Impossible de charger la liste des cars.");
            }
        },

        dateFormatation(date) {
            return formatFrenchDate(date);
        },

        reloadChart() {
            this.i++;
        },

        async startAnomaly(mtrl) {
            try {
                let typeAnom = await Swal.fire({
                    title: "Précisez la nature de l'anomalie:",
                    icon: "question",
                    html:
                        '<select id="anomalyType" class="form-select">' +
                        '<option value="" disabled selected>Choisir une option</option>' +
                        '<option value="mecanique">Problème mécanique</option>' +
                        '<option value="technique">Problème technique</option>' +
                        "</select>",
                    showCancelButton: true,
                    confirmButtonText: "Déclarer",
                    cancelButtonText: "Annuler",
                    preConfirm: () => {
                        const anomalyType =
                            Swal.getPopup().querySelector("#anomalyType").value;
                        if (!anomalyType) {
                            Swal.showValidationMessage(
                                "Veuillez sélectionner une nature d'anomalie",
                            );
                        }
                        return anomalyType;
                    },
                });

                if (!typeAnom.isConfirmed) {
                    return;
                }

                await axios.post(`/cars/${encodeURIComponent(mtrl)}/anomalies`, {
                    description: typeAnom.value,
                });
                debugLog("fleet", "Anomalie créée.", { car: mtrl });
                Swal.fire({
                    title: "Confirmation",
                    text: `Nouvelle anomalie créer !!
                    Car ${mtrl} mis hors service.`,
                });
                await this.fetchCars();
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Erreur",
                    text: error.response?.data?.message || "Impossible d'enregistrer l'anomalie.",
                });
            }
        },

        newCar() {
            this.cars.push({ matricule: "", nouveau: true });
        },

        async saveNewCar(mtrl) {
            try {
                let response = await axios.post("/cars", { matricule: mtrl });
                debugLog("fleet", "Car créé.", { car: mtrl });

                Swal.fire({
                    icon: "success",
                    title: "Succès",
                    text: response.data.message,
                });

                this.fetchCars();
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Erreur",
                    text:
                        err.response?.data?.message ||
                        "Une erreur est survenue lors de l'enregistrement du car.",
                });
            }
        },

        async deleteCar(matricule) {
            try {

                const confirmation = await Swal.fire({
                    title: "Êtes-vous sûr ?",
                    text: `Voulez-vous vraiment supprimer le car ${matricule} ?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Oui, supprimer",
                    cancelButtonText: "Annuler",
                });

                if (!confirmation.isConfirmed) return;

                await axios.delete(`/cars/${encodeURIComponent(matricule)}`);
                debugLog("fleet", "Car supprimé.", { car: matricule });
                await Swal.fire(
                    "Supprimé!",
                    `Le car ${matricule} a été supprimé.`,
                    "success",
                );
                await this.fetchCars();
            } catch (error) {
                this.showError(error, "Impossible de supprimer ce car.");
            }
        },

        async fetchDailyMoov() {
            try {
                const response = await axios.get("/movements");
                this.daily_moov = response.data;
                debugLog("fleet", "Mouvements du jour chargés.", {
                    count: this.daily_moov.length,
                });
            } catch (error) {
                this.showError(error, "Impossible de charger les mouvements du jour.");
            }
        },

        async saveDailyMoov(mtrl, distance, km) {
            try {
                if (distance <= 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Erreur",
                        text: "La distance parcourue doit être un nombre positif.",
                    });
                    return;
                }

                await axios.post("/movements", {
                    mtrl_car: mtrl,
                    distance_parcourue: distance,
                });
                debugLog("fleet", "Mouvement enregistré.", { car: mtrl });

                if (km === 0) {
                    await Swal.fire({
                        icon: "success",
                        title: "Succès",
                        text: "Mouvement enregistré avec succès.",
                    });
                } else {
                    await Swal.fire({
                        icon: "success",
                        title: "Succès",
                        text: "Modification enregistrée.",
                    });
                }

                await this.fetchDailyMoov();
            } catch (error) {
                this.showError(error, "Impossible d'enregistrer ce mouvement.");
            } finally {
                this.reloadChart();
            }
        },

        async fetchCurrentAnomalies() {
            try {
                const response = await axios.get("/anomalies");
                this.current_anomalies = response.data;
                debugLog("fleet", "Anomalies chargées.", {
                    count: this.current_anomalies.length,
                });
            } catch (error) {
                this.showError(error, "Impossible de charger les anomalies.");
            }
        },

        async EndAnomaly(id, mtrl_car) {
            try {
                const response = await axios.patch(`/anomalies/${id}`);
                debugLog("fleet", "Anomalie clôturée.", { anomalyId: id, car: mtrl_car });

                Swal.fire({
                    icon: "success",
                    title: "Succès",
                    text: response.data.message,
                });

                await this.fetchCurrentAnomalies();
                await this.fetchCars();
            } catch (error) {
                this.showError(error, "Impossible de clôturer cette anomalie.");
            }
        },

        async findAnom() {
            try {
                let response = await axios.get("/anomalies", {
                    params: {
                        matricule: this.filter,
                    },
                });

                this.current_anomalies = response.data;
                this.resetdisplay =
                    "display: flex, justify-content: flex-end !important";
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Erreur",
                    text: err.response?.data?.message || "Impossible de rechercher les anomalies.",
                });
            }
        },

        reset() {
            this.filter = "";
            this.resetdisplay = "display: none";
            this.fetchCurrentAnomalies();
        },

        showError(error, fallback) {
            return showRequestError(error, fallback);
        },

    },

    async mounted() {
        await Promise.all([
            this.fetchCars(),
            this.fetchDailyMoov(),
            this.fetchCurrentAnomalies(),
        ]);
        await initTooltips();
    },

    watch: {
        cars: {
            async handler() {
                await initTooltips();
            },
            deep: true,
        },
    },
};
</script>
