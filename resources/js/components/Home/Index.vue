<template>
    <div>
        <div class="row mb-4">
            <div class="col-12">
                <hotel-select @hotel='hotelId = $event'></hotel-select>
            </div>
        </div>

        <template v-if="hotelId.length > 0">
            <icon-cards :hotel-id='hotelId'></icon-cards>
        </template>

        <div class="row">
            <div class="col-12 col-sm-6">
                <voucher-card-list></voucher-card-list>
            </div>
            <div class="col-12 col-sm-6">
                <guest-chart-card :hotel-id='hotelId'></guest-chart-card>
                    </div>
                    <div class="card-body">
                        <canvas id="myAreaChart" width="100%" height="30"></canvas>
                    </div>
                    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import IconCards from './IconCards'
    import VoucherCardList from '../Vouchers/CardList'
    import GuestChartCard from '../Guests/ChartCard'

    export default {
        data() {
            return {
                hotelId: ''
            }
        },
        components: {
            IconCards,
            GuestChartCard,
        }
    }
</script>
