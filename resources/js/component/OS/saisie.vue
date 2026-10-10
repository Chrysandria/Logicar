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
                <button
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#newPlanning"
                >
                    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                    Nouveau
                </button>
            </div>
        </header>

        <div
            v-if="plannings.length == 0"
            class="w-100 flex-fill d-flex gap-4 flex-column align-items-center justify-content-center mt-5 pt-5"
            style="opacity: 0.8"
        >
            <h1 class="text-center">❌</h1>
            <p class="text-center fw-semibold">
                Aucun planning Enregistrer pour le moment !!
            </p>
        </div>

        <div class="card shadow-sm mb-4" v-for="planning in plannings">
            <div class="card-header bg-primary text-white">
                <h3>
                    Heure de prise de service:
                    {{ planning.HPS.HPS.substring(0, 5) }}
                </h3>
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
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in planning.emps">
                                <td>{{ emp.matricule }}</td>
                                <td>{{ emp.nom }}</td>
                                <td>{{ emp.telephone }}</td>
                                <td>{{ emp.lieu_ramassage }}</td>
                                <td>{{ emp.pnt_ramassage_dec }}</td>
                                <td>{{ emp.pnt_ramassage_adm }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div
        class="fade modal"
        tabindex="-1"
        id="newPlanning"
        aria-hidden="true"
        aria-labelledby="#modalTitle"
    >
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalTitle">
                        Nouveau planning
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-12 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-light"
                                        ><label
                                            for="hps"
                                            class="form-label fw-semibold"
                                            >Heure de prise de service:
                                        </label></span
                                    >
                                    <input
                                        type="time"
                                        name="hps"
                                        id="hps"
                                        v-model="hps"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="error_message"
                        class="alert alert-danger planning-entry-error d-flex align-items-start gap-2"
                        role="alert"
                    >
                        <i class="bi bi-exclamation-circle-fill mt-1" aria-hidden="true"></i>
                        <span>{{ error_message }}</span>
                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-stripped table-bordered w-auto text-center"
                        >
                            <thead class="table-dark bg-dark">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Telephone</th>
                                    <th>
                                        Lieu de ramassage
                                    </th>
                                    <th>
                                        Point de ramassage décalée
                                    </th>
                                    <th>
                                        Point de ramassage ADM
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(emp, index) in list_emps"
                                    :key="index"
                                >
                                    <td>
                                        <input
                                            type="text"
                                            inputmode="numeric"
                                            ref="input"
                                            class="form-control"
                                            v-model="emp.matricule"
                                            @keydown.enter.prevent="
                                                autoCompleteMatricule(index)
                                            "
                                            enterkeyhint="enter"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            title="Saisissez le matricule d'un employé, puis cliquer sur la touche 'Entrer' pour l'autocomplétion des autres céllules."
                                        />
                                    </td>
                                    <td>{{ emp.nom }}</td>
                                    <td>{{ emp.telephone }}</td>
                                    <td>{{ emp.lieu_ramassage }}</td>
                                    <td>{{ emp.pnt_ramassage_dec }}</td>
                                    <td>{{ emp.pnt_ramassage_adm }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="savePlanning()"
                    >
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { nextTick } from "vue";
import {
    closeLoading,
    fetchPlannings,
    formatFrenchDate,
    initTooltips,
    showLoading,
    showRequestError,
} from "../../utils/interface.js";
import { debugLog } from "../../utils/logger.js";

export default {
    data() {
        return {
            date: "",
            hps: "",
            emps_in_db: [],
            service: "",
            plannings: [],
            list_emps: [
                {
                    matricule: "",
                    nom: "",
                    telephone: "",
                    lieu_ramassage: "",
                    pnt_ramassage_dec: "",
                    pnt_ramassage_adm: "",
                },
            ],
            error_message: "",
        };
    },

    methods: {
        dateFormatation() {
            this.date = formatFrenchDate(this.$route.params.date);
        },

        async getEmps() {
            try {
                let service = this.$route.params.service;
                let response = await axios.get("/employees", {
                    params: {
                        service: service,
                    },
                });
                this.emps_in_db = response.data;
                debugLog("planning-entry", "Employés chargés.", {
                    count: this.emps_in_db.length,
                    service,
                });
            } catch (err) {
                debugLog("planning-entry", "Échec du chargement des employés.", {
                    status: err.response?.status ?? "network",
                });
                console.error(err);
            }
        },

        incrementAndPassFocus() {
            const lastIndex = this.list_emps.length - 1;
            if (this.list_emps[lastIndex].matricule !== "") {
                this.list_emps.push({
                    matricule: "",
                    nom: "",
                    telephone: "",
                    lieu_ramassage: "",
                    pnt_ramassage_dec: "",
                    pnt_ramassage_adm: "",
                });
            }
            this.$nextTick(() => {
                const inputs = this.$refs.input;
                if (inputs.length > 0) {
                    inputs[inputs.length - 1].focus();
                }
            });
        },

        autoCompleteMatricule(index) {
            const validate = this.verifIfUniqueMatricule();
            if (!validate) {
                return;
            }
            const enteredMatricule = this.list_emps[index].matricule;

            const emp = this.emps_in_db.find(
                (e) => e.matricule === enteredMatricule,
            );
            if (emp) {
                debugLog("planning-entry", "Matricule reconnu.", {
                    row: index + 1,
                });
                this.list_emps[index].nom = emp.nom;
                this.list_emps[index].lieu_ramassage = emp.lieu_ramassage;
                this.list_emps[index].telephone = emp.telephone;
                this.list_emps[index].pnt_ramassage_dec = emp.pnt_ramassage_dec;
                this.list_emps[index].pnt_ramassage_adm = emp.pnt_ramassage_adm;
            } else {
                debugLog("planning-entry", "Matricule non reconnu.", {
                    row: index + 1,
                });
                this.error_message = `Matricule ${enteredMatricule} non trouvé. Veuillez vérifier.`;
                return;
            }
            this.incrementAndPassFocus();
        },

        verifIfUniqueMatricule() {
            for (let i = 0; i < this.list_emps.length; i++) {
                let count = 0;
                for (let j = 0; j < this.list_emps.length; j++) {
                    if (
                        this.list_emps[i].matricule ===
                            this.list_emps[j].matricule &&
                        this.list_emps[i].matricule !== ""
                    ) {
                        count++;
                    }
                }
                if (count > 1) {
                    debugLog("planning-entry", "Matricule en doublon.", {
                        row: i + 1,
                    });
                    this.error_message = `Matricule ${this.list_emps[i].matricule} dupliqué. Veuillez vérifier.`;
                    return false;
                }
            }
            this.error_message = "";
            return true;
        },

        async getPlannings() {
            try {
                this.plannings = await fetchPlannings(
                    this.$route.params.date,
                    this.$route.params.service,
                );
                debugLog("planning-entry", "Plannings chargés.", {
                    count: this.plannings.length,
                    date: this.$route.params.date,
                });
            } catch (err) {
                debugLog("planning-entry", "Échec du chargement des plannings.", {
                    status: err.response?.status ?? "network",
                });
                console.error(err);
            }
        },

        async savePlanning() {
            if (this.hps === "") {
                debugLog("planning-entry", "Enregistrement refusé : HPS vide.");
                this.error_message = "Veuillez remplir le champ HPS.";
                return;
            }

            if (
                this.list_emps.length === 0 ||
                (this.list_emps.length === 1 &&
                    this.list_emps[0].matricule === "")
            ) {
                debugLog("planning-entry", "Enregistrement refusé : aucun employé.");
                this.error_message = "Veuillez saisir au moins un employé.";
                return;
            }

            this.list_emps = this.list_emps.filter(
                (emp) => emp.matricule !== "" && emp.nom !== "",
            );

            showLoading("Traitement en cours");
            debugLog("planning-entry", "Enregistrement du planning demandé.", {
                date: this.$route.params.date,
                employeeCount: this.list_emps.length,
                hps: this.hps,
            });

            try {
                let response = await axios.post(
                    "/plannings",
                    {
                        employees: this.list_emps.map((employee) => ({
                            matricule: employee.matricule,
                        })),
                        hps: this.hps,
                        date: this.$route.params.date,
                    }
                );
                debugLog("planning-entry", "Planning enregistré.", {
                    date: this.$route.params.date,
                    employeeCount: this.list_emps.length,
                    hps: this.hps,
                });

                closeLoading();

                Swal.fire({
                    icon: "success",
                    title: "Succès",
                    text: response.data.message,
                });

                this.dateTime = "";
                this.type = "";
                this.list_emps = [
                    {
                        matricule: "",
                        nom: "",
                        telephone: "",
                        lieu_ramassage: "",
                        pnt_ramassage_dec: "",
                        pnt_ramassage_adm: "",
                    },
                ];
                ((this.error_message = ""),
                    (this.hps = ""),
                    this.getPlannings());
            } catch (err) {
                debugLog("planning-entry", "Échec de l'enregistrement du planning.", {
                    status: err.response?.status ?? "network",
                });
                closeLoading();
                showRequestError(
                    err,
                    "Une erreur est survenue lors de l'enregistrement du planning.",
                );
            }
        },
    },

    async mounted() {
        showLoading();
        try {
            await Promise.all([this.getEmps(), this.getPlannings()]);
        } finally {
            closeLoading();
        }
        this.dateFormatation();
        await initTooltips();
    },

    watch: {
        "$route.params.date"(newDate) {
            if (newDate) this.dateFormatation();
        },

        list_emps: {
            deep: true,
            async handler() {
                await nextTick();
                await initTooltips();
            },
        },
    },
};
</script>
