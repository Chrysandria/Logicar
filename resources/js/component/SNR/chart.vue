<template>
    <div class="row">
        <div class="chart-container col-12 col-lg-6">
            <BarChartJS :data="chartData" :chart-options="chartOptions" />
        </div>
        <div class="table-responsive col-12 col-lg-6 p-5 chart-container">
            <table class="table table-striped table-bordered">
                <thead class="table-dark text-white"><tr><th>Matricule</th><th>Kilomètres parcourus</th></tr></thead>
                <tbody>
                    <tr v-for="item in chartData.datasets[0].data" :key="item">
                        <td>{{ chartData.labels[chartData.datasets[0].data.indexOf(item)] }}</td>
                        <td>{{ item }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Bar } from 'vue-chartjs';
import axios from 'axios';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

export default defineComponent({
    name: 'BarChart',
    components: {
        BarChartJS: Bar,
    },

    data() {
        return {
            chartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Kilomètres parcourus',
                        backgroundColor: 'rgba(0,0,255,0.5)',
                        data: [],
                    },
                ],
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 200,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Chart.js Bar Chart',
                    },
                },
            },
        };
    },

    methods: {
        async fetchData() {
            try {
                const response = await axios.get('/moov');
                const data = response.data.data;
                console.log(data);

                this.chartData = {
                    labels: data.map(item => item.matricule),
                    datasets: [
                        {
                            label: `Mouvement des car pour le mois de ${response.data.month} ${response.data.year} (en km)`,
                            backgroundColor: 'rgba(0,0,255,0.5)',
                            data: data.map(item => item.total),
                        },
                    ],
                };
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        },
    },

    async mounted() {
        await this.fetchData();
    },
});
</script>

<style scoped>

</style>
