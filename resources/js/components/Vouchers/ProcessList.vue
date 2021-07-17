<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light app-nav border border-top-0 border-right-0 border-left-0">
            <a v-if="$can('vouchers.index')" href="/vouchers" class="navbar-brand text-muted">
                {{ $t('vouchers.title') }}
            </a>
            <button type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbarNavDropdown" class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item" v-if="$can('vouchers.edit')">
                        <a href="#" class="nav-link" v-if="vouchers.length != 0" @click.prevent="process">
                            {{ $t('common.continue') }}
                        </a>
                    </li>
                    <li class="nav-item" v-if="$can('vouchers.index')">
                        <a href="/vouchers" class="nav-link">
                            {{ $t('common.back') }}
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="row my-4">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group">
                    <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="updateVoucherList">
                        <option v-for="hotel in hotels" :key="hotel.hash" :value="hotel.hash">
                            {{ hotel.business_name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="crud-list">
            <div class="crud-list-heading mt-2">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>{{ $t('common.number') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <h5>Hotel</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <h5>{{ $t('vouchers.customer') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <h5>{{ $t('common.value') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>{{ $t('payments.payment') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>{{ $t('common.type') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>{{ $t('common.date') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>{{ $t('common.status') }}</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>{{ $t('common.options') }}</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" v-if="vouchers.length != 0">
                <div class="crud-list-row" v-for="voucher in vouchers" :key="voucher.hash">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center dont-break-out">
                            <p>
                                <a :href="'/vouchers/' +voucher.hash">
                                    {{voucher.number }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>
                                <a :href="'/hotels/' + hotel">
                                    {{ getSelectedHotel().business_name }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p v-if="voucher.company">
                                <a :href="'/companys/' +voucher.company.hash">
                                    {{voucher.company.business_name }}
                                </a>
                            </p>
                            <p v-else-if="voucher.guests.length > 0">
                                <a :href="'/guests/' +voucher.guests[0].hash">
                                    {{voucher.guests[0].name }} {{voucher.guests[0].last_name }}
                                </a>
                            </p>
                            <p v-else>
                                {{ $t('vouchers.registerGuests') }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>
                                $ {{ new Intl.NumberFormat("de-DE").format(voucher.value) }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1 align-self-center">
                            <p>
                                {{ calculatePaymentPercentage(voucher) }}%
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center">
                            <p>
                                {{ voucher.reservation ? $t('vouchers.reservation') : $t('vouchers.checkin') }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center">
                            <p>
                                {{ voucher.created_at }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-1 col-md-1 col-lg- align-self-center">
                            <p v-if="voucher.open"><i class="text-primary fa fa-lock-open fa-2x"></i></p>
                            <p v-else><i class="text-primary fa fa-lock fa-2x"></i></p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <a href="#" @click.prevent="removeVoucher(voucher)">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="row" v-for="room in voucher.rooms" :key="room.hash">
                        <template v-if="checkRoomIsEnabled(room) == true">
                            <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center dont-break-out">
                                <p class="text-right">
                                    <i class="fas fa-caret-right"></i>
                                </p>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                                <p>
                                    {{ $t('rooms.room') }} No. {{ room.number }}
                                </p>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                                <p>{{ $t('vouchers.nights') }}: {{ room.pivot.quantity }}</p>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                                <p>{{ $t('common.endDate') }}: {{ room.pivot.end }}</p>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                                <p>Total: $ {{ new Intl.NumberFormat("de-DE").format(room.pivot.value) }}</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="crud-list-items" v-else>
                <div class="crud-list-row">
                    <div class="card mt-4">
                        <div class="card-body">
                            {{ $t('common.noRecords') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        if (this.hotels.length > 0) {
            this.hotel = _.first(this.hotels).hash
            this.vouchers = _.first(this.hotels).vouchers
            this.hasRooms()
        }
    },
    props: ['hotels'],
    data() {
        return {
            hotel: null, // The current hotel hash
            vouchers: []
        }
    },
    methods: {
        updateVoucherList() {
            _.map(this.hotels, (headquarters) => {
                // Filter by current hotel hash
                if (headquarters.hash == this.hotel) {
                    this.vouchers = headquarters.vouchers

                    this.hasRooms()
                }
            })
        },
        hasRooms() {
            this.vouchers = _.filter(this.vouchers, (voucher) => {
                voucher.rooms = _.filter(voucher.rooms, (room) => {
                    return this.checkRoomIsEnabled(room)
                })

                return voucher.rooms.length > 0
            })
        },
        checkRoomIsEnabled(room) {
            return room.pivot.enabled && moment(room.pivot.end).isBefore(moment().add(1, 'days').format('YYYY-MM-DD'))
        },
        getSelectedHotel() {
            return _.find(this.hotels, (headquarters) => {
                return headquarters.hash == this.hotel;
            })
        },
        calculatePaymentPercentage(voucher) {
            let percentage = 0
            let sum = 0

            if (voucher.payments.length != 0) {
                sum = _.reduce(voucher.payments, (memo, payment) => {
                    return memo + parseFloat(payment.value)
                }, 0);
            }

            percentage = (sum / parseFloat(voucher.value)) * 100

            return percentage.toFixed(2)
        },
        removeVoucher(voucher) {
            this.vouchers = _.filter(this.vouchers, (item) => {
                return item.hash !=voucher.hash
            })
        },
        process() {
            let numbers = []

            if (this.vouchers.length > 0) {
                _.map(this.vouchers, (voucher) => {
                    numbers.push(voucher.number)
                })

                axios.post('/vouchers/process', {
                    numbers: numbers,
                    hotel: this.hotel
                }).then(response => {
                    let processed = Array.from(response.data.processed)

                    processed.forEach((number, index) => {
                        this.vouchers = _.filter(this.vouchers, (voucher) => {
                            return voucher.number != number
                        })
                    })

                    if (this.vouchers.length > 0) {
                        toastr.error(
                            this.$root.$t('vouchers.incomplete.processing'),
                            this.$root.$t('common.sorry')
                        );
                    } else {
                        toastr.success(
                            this.$root.$t('vouchers.complete.processing'),
                            this.$root.$t('common.successful')
                        );
                    }
                }).catch(e => {
                    toastr.error(
                        this.$root.$t('common.try'),
                        'Error'
                    );
                });
            } else {
                toastr.error(
                    this.$root.$t('common.noRecords'),
                    'Error'
                );
            }
        }
    },
};
</script>
