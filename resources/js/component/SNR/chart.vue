<template>
    <div class="row g-4">
        <div class="chart-panel col-12 col-xl-6">
            <div class="chart-container">
                <BarChartJS :data="chartData" :chart-options="chartOptions" />
            </div>
        </div>
        <div class="chart-panel col-12 col-xl-6">
            <div class="chart-container">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark text-white">
                            <tr>
                                <th>Matricule</th>
                                <th>Kilomètres parcourus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in chartData.datasets[0].data" :key="chartData.labels[index]">
                                <td>{{ chartData.labels[index] }}</td>
                                <td>{{ item }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                        backgroundColor: 'rgba(21,94,239,0.65)',
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
                        text: 'Kilomètres parcourus par car',
                    },
                },
            },
        };
    },

    methods: {
        async fetchData() {
            try {
                const response = await axios.get('/movements/monthly');
                const data = response.data.data;

                this.chartData = {
                    labels: data.map(item => item.matricule),
                    datasets: [
                        {
                            label: `Mouvement des car pour le mois de ${response.data.month} ${response.data.year} (en km)`,
                            backgroundColor: 'rgba(21,94,239,0.65)',
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
