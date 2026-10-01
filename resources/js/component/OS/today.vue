<template>
    <div class="container-fluid">
        <div class="row mb-4 bg-white">
            <div class="w-100 d-flex justify-content-between">
                <div>
                    <h4>Planning du : {{ date }}</h4>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary rounded-5" type="button" data-bs-toggle="modal"
                        data-bs-target="#search"><i class="bi bi-search"></i> Rechercher</button>
                </div>
            </div>
        </div>

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
                    <div v-if="results.length == 0" v-html="message">

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
import Swal from 'sweetalert2';

export default {
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
            let initialDate = new Date();
            const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
            let Formateddate = new Intl.DateTimeFormat("fr-FR", options).format(initialDate);

            this.date = Formateddate;
        },

        async getPlannings() {
            try {
                let service = localStorage.getItem("service");
                console.log(service);

                let today = new Date().toLocaleDateString("en-CA");
                console.log(today);


                let response = await axios.get("/get-plannings", {
                    params: {
                        date: today,
                        service: service
                    }
                });

                console.log(response.data);
                this.plannings = response.data

            } catch (err) {
                console.error(err);

            }
        },

        async filterPlannings() {
            Swal.fire({
                title: "Veuilez patienter...",
                text: "Recherche en cours",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            try {
                let service = localStorage.getItem("service");
                console.log(service);

                let response = await axios.get("/get-plannings", {
                    params: {
                        date: this.filter,
                        service: service
                    }
                });

                console.log(response.data);
                if (response.data.length === 0) {
                    this.results = [];
                    this.message = `<div class="w-100 flex-fill d-flex gap-4 flex-column align-items-center justify-content-center mt-5 pt-5" style="opacity: 0.8;">
                        <h1 class="text-center">🔍</h1>
                        <p class="text-center fw-semibold">Aucun résultat trouvé pour cette date !!</p>
                      </div>`
                } else {
                    this.results = response.data
                }
                Swal.close()

            } catch (err) {
                console.error(err);
                Swal.close();

            }
        },

        reset() {
            this.filter = "";
            this.results = [];
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
        Swal.close();
        this.dateFormatation()
    },

}
</script>
