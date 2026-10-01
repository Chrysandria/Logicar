<template>
    <div class="container-fluid p-3">
        <div class="row mb-4 bg-white">
            <div class="col">
                <h6>Planning du : {{ date }}</h6>
            </div>

            <div class="col d-flex align-items-end justify-content-end">
                <button
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#newPlanning"
                >
                    Nouveau
                </button>
            </div>
        </div>

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
                        <h2 class="text-center text-danger fw-semibold">
                            {{ error_message }}
                        </h2>
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
import * as bootstrap from "bootstrap";
import { nextTick } from "vue";

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
            let initialDate = new Date(this.$route.params.date);
            const options = {
                year: "numeric",
                month: "numeric",
                day: "numeric",
            };
            let Formateddate = new Intl.DateTimeFormat("fr-FR", options).format(
                initialDate,
            );

            this.date = Formateddate;
        },

        initTooltips() {
            document
                .querySelectorAll("[data-bs-toggle='tooltip']")
                .forEach((el) => {
                    const instance = bootstrap.Tooltip.getInstance(el);
                    if (instance) instance.dispose();
                });

            document
                .querySelectorAll("[data-bs-toggle='tooltip']")
                .forEach((el) => {
                    new bootstrap.Tooltip(el, {
                        placement: "right",
                    });
                });
        },

        async getEmps() {
            try {
                let service = this.$route.params.service;
                let response = await axios.get("/emps", {
                    params: {
                        service: service,
                    },
                });
                this.emps_in_db = response.data;
                console.log(this.emps_in_db);
            } catch (err) {
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
            console.log(enteredMatricule);

            const emp = this.emps_in_db.find(
                (e) => e.matricule === enteredMatricule,
            );
            if (emp) {
                this.list_emps[index].nom = emp.nom;
                this.list_emps[index].lieu_ramassage = emp.lieu_ramassage;
                this.list_emps[index].telephone = emp.telephone;
                this.list_emps[index].pnt_ramassage_dec = emp.pnt_ramassage_dec;
                this.list_emps[index].pnt_ramassage_adm = emp.pnt_ramassage_adm;
            } else {
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
                    this.error_message = `Matricule ${this.list_emps[i].matricule} dupliqué. Veuillez vérifier.`;
                    return false;
                }
            }
            this.error_message = "";
            return true;
        },

        async getPlannings() {
            try {
                let response = await axios.get("/get-plannings", {
                    params: {
                        date: this.$route.params.date,
                        service: this.$route.params.service,
                    },
                });

                console.log(response.data);
                this.plannings = response.data;
            } catch (err) {
                console.error(err);
            }
        },

        async savePlanning() {
            if (this.hps === "") {
                this.error_message = "Veuillez remplir le champ HPS.";
                return;
            }

            if (
                this.list_emps.length === 0 ||
                (this.list_emps.length === 1 &&
                    this.list_emps[0].matricule === "")
            ) {
                this.error_message = "Veuillez saisir au moins un employé.";
                return;
            }

            this.list_emps = this.list_emps.filter(
                (emp) => emp.matricule !== "" && emp.nom !== "",
            );

            Swal.fire({
                title: "Veuilez patienter...",
                text: "Traitement en cours",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            try {
                let response = await axios.post(
                    "/save_planning",
                    {
                        list_emps: this.list_emps,
                        hps: this.hps,
                        date: this.$route.params.date,
                    }
                );

                Swal.close();

                Swal.fire({
                    icon: "success",
                    title: "Succès",
                    text: response.data.message,
                });

                this.dateTime = "";
                this.type = "";
                this.list_emps = [
                    { matricule: "", nom: "", lieu_ramassage: "", phone: "" },
                ];
                ((this.error_message = ""),
                    (this.hps = ""),
                    this.getPlannings());
            } catch (err) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Erreur",
                    text:
                        err.response.data.message ||
                        "Une erreur est survenue lors de l'enregistrement du planning.",
                });
                console.error(err.response);

            } finally {
                console.log("Save planning attempt finished.");
            }
        },
    },

    async mounted() {
        Swal.fire({
            title: "Veuilez patienter...",
            text: "Chargement en cours",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
        await this.getEmps();
        await this.getPlannings();
        Swal.close();
        this.dateFormatation();
        this.initTooltips();
    },

    watch: {
        "$route.params.date"(newDate) {
            if (newDate) this.dateFormatation();
        },

        list_emps: {
            deep: true,
            async handler() {
                await nextTick();
                this.initTooltips();
            },
        },
    },
};
</script>
