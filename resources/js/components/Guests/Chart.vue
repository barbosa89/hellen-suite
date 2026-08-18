<template>
    <bar
        id="my-chart-id"
        :options="chartOptions"
        :data="chartData">
    </bar>
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    components: {
        Bar
    },
    props: {
        chartData: {
            type: Object,
            default: function () {
                return {}
            }
        }
    },
    data() {
        return {
            chartOptions: {
                responsive: true
            }
        }
    }
}
</script>
