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
        </div>
        <div class="row" v-if="hotelId.length > 0">
            <div class="col">
                <note-card-list :hotel-id='hotelId'></note-card-list>
            </div>
        </div>
    </div>
</template>

<script>
    import IconCards from './IconCards'
    import VoucherCardList from '../Vouchers/CardList'
    import GuestChartCard from '../Guests/ChartCard'
    import NoteCardList from '../Notes/CardList'

    export default {
        data() {
            return {
                hotelId: ''
            }
        },
        components: {
            IconCards,
            VoucherCardList,
            GuestChartCard,
            NoteCardList,
        }
    }
</script>
