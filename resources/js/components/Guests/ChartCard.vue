<template>
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-users"></i>
            {{ $t('guests.title') }}
        </div>
        <div class="card-body">
            <template v-if="showChart">
                <chart :chart-data='chartData'></chart>
            </template>
            <template v-else>
                <em class="fas fa-spinner fa-pulse"></em>
            </template>
        </div>
        <div class="card-footer small text-muted">{{ $t('common.updated.at') }}: {{ date.format('YY-MM-DD HH:mm:ss') }}</div>
    </div>
</template>

<script>
import moment from 'moment'
    import Chart from './Chart'

    export default {
        props: {
            hotelId: {
                type: String,
                default: function () {
                    return ''
                }
            }
        },
        data() {
            return {
                chartData: {},
                date: moment()
            }
        },
        components: {
            Chart
        },
        mounted() {
            this.getChartData()
        },
        watch: {
            hotelId() {
                this.getChartData()
            }
        },
        computed: {
            showChart() {
                return _.isEmpty(this.chartData) == false
            }
        },
        methods: {
            getChartData() {
                if (this.hotelId) {
                    axios
                        .get(route('api.web.vouchers.datasets.guests', {
                            'hotel': this.hotelId,
                            'period': this.getPeriod()
                        }))
                        .then(response => {
                            this.chartData = response.data
                        })
                }
            },
            getPeriod() {
                return moment().format('YY-MM-DD')
            }
        }
    }
</script>
