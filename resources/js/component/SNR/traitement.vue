<template>
    <div class="container-fluid">
        <div class="row mb-4 bg-white">
            <div class="col">
                <h4>Planning du : {{ date }}</h4>
            </div>
        </div>

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
                    <div class="mb-4 d-flex gap-1 flex-column" id="form">
                        <div class="d-flex justify-content-between">
                            <div class="input-group w-25">
                                <span class="input-group-text bg-light"><label for="hps"
                                        class="form-label fw-semibold">Heure de début de ramassage: </label></span>
                                <input type="time" name="hps" id="hps" v-model="hdr" class="form-control">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recap"
                                    @click="able()">Recapitulation</button>
                            </div>
                        </div>
                        <div class="input-group w-25 mt-4">
                            <span class="input-group-text bg-light"><label for="hps"
                                    class="form-label fw-semibold">Trajet:</label></span>
                            <select name="trajet" id="trajet" class="form-select" v-model="id_trajet">
                                <option value="" class="disabled-value">Choisir un trajet</option>
                                <option v-for="(trajet, i) in trajets" :key="i" :value="trajet.id"> {{ trajet.designation
                                    }} </option>
                            </select>
                        </div>
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
                <div class="modal-header bg-light mb-4">
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
            let initialDate = new Date(this.$route.params.date);
            const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
            let Formateddate = new Intl.DateTimeFormat("fr-FR", options).format(initialDate);

            this.date = Formateddate;
        },

        async getPlannings() {
            try {
                let response = await axios.get("/gap", {
                    params: {
                        date: this.$route.params.date,
                    }
                });

                console.log(response.data);
                this.plannings = response.data

            } catch (err) {
                console.error(err);

            }
        },

        async setTriParams(h) {
            this.hps = h.substring(0, 5);
            let response = await axios.get("/pwr", {
                params: {
                    date: this.$route.params.date,
                    hps: this.hps
                }
            });
            console.log(response.data);

            this.PAT = response.data;

            let response2 = await axios.get("/pr", {
                params: {
                    date: this.$route.params.date,
                    hps: this.hps
                }
            });

            this.details = response2.data;
            //console.log(this.details);
            console.log(this.details[0].ramassage);


            console.log(response2.data);


        },

        async save() {
            try {
                console.log(this.sub);
                let ramassageResponse = await axios.post("/create_ramassage", {
                    HDR: this.hdr,
                    id_trajet: this.id_trajet,
                    plannings: this.sub
                });

                console.log(ramassageResponse.data);
                this.setTriParams(this.hps);
                this.sub = [];
                this.id_trajet = "";
                this.hdr = "";

            } catch (err) {
                console.error(err);
            }
        },

        able() {
            if (this.PAT.length === 0) this.dsb = false;
        },

        async getAllTrajet() {
            try {
                let response = await axios.get("/allTrajet");
                this.trajets = response.data;

                console.log(this.trajets);
            } catch (err) {
                console.error(err);
            }
        },

        async generatePdf() {
            try {
                let response = await axios.post("/pdf", {
                    date: this.$route.params.date,
                    hps: this.hps,
                    data: this.details
                }, {
                    responseType: 'arraybuffer'
                });

                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                window.open(url);
            } catch (err) {
                console.error(err);
                alert("Pdf non générer");
            }
        }

    },

    async mounted() {
        Swal.fire({
                title: "Veuilez patienter...",
                text: "Chargement en cours",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        await this.getPlannings();
        await this.getAllTrajet();
        Swal.close();
        this.dateFormatation();
    },

}
</script>
