<template>
    <div>
        <div class="row">
            <div class="col">
                <div>
                    <h1><small>{{ $t('common.hi') }}, {{ userName }}</small> <span id="v-step-0"></span></h1>
                </div>
            </div>
        </div>

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
    import NoteCardList from '../Notes/CardList'
    import GuestChartCard from '../Guests/ChartCard'
    import VoucherCardList from '../Vouchers/CardList'

    export default {
        components: {
            IconCards,
            NoteCardList,
            GuestChartCard,
            VoucherCardList,
        },
        props: {
            userName: {
                type: String,
                default: function () {
                    return ''
                }
            }
        },
        data() {
            return {
                hotelId: ''
            }
        },
    }
</script>
