<script>
    import { Bar } from 'vue-chartjs'

    export default {
        extends: Bar,
        props: {
            chartData: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        },
        mounted () {
            this.renderChart(this.chartData)
        }
    }
</script>
