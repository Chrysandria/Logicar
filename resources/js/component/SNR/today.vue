<template>
    <div class="container-fluid">
        <div class="row mb-4 bg-white">
            <div class="w-100 d-flex justify-content-between">
                <div>
                    <h4>Planning du : {{ date }}</h4>
                </div>
            </div>
        </div>

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
            let initialDate = new Date();
            const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
            let Formateddate = new Intl.DateTimeFormat("fr-FR", options).format(initialDate);

            this.date = Formateddate;
        },

        async getPlannings() {
            try {
                let today = new Date().toLocaleDateString("en-CA");
                console.log(today);


                let response = await axios.get("/gap", {
                    params: {
                        date: today,
                    }
                });

                console.log(response.data);
                this.plannings = response.data

            } catch (err) {
                console.error(err);

            }
        },

        async set_non_recups(id){
            try {
                let response = await axios.put("/set_non_recups", {
                        id: id
                });

                console.log(response.data);
                await this.getPlannings();

            } catch (err) {
                console.error(err);

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
        Swal.close();
        this.dateFormatation()
    },

}
</script>
