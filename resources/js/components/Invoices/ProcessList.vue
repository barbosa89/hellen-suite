<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light app-nav border border-top-0 border-right-0 border-left-0">
            <a href="/invoices" class="navbar-brand text-muted">
                {{ $t('invoices.title') }}
            </a>
            <button type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbarNavDropdown" class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="#" class="nav-link" v-if="invoices.length != 0" @click.prevent="process">
                            {{ $t('common.continue') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/invoices" class="nav-link">
                            {{ $t('common.back') }}
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="row my-4">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group">
                    <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="updateInvoiceList">
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
                        <h5>Número</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <h5>Hotel</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <h5>Cliente</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <h5>Valor</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>Pago</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>Tipo</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>Fecha</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
                        <h5>Estado</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>Opciones</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" v-if="invoices.length != 0">
                <div class="crud-list-row" v-for="invoice in invoices" :key="invoice.hash">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <p>
                                <a :href="'/invoices/' + invoice.hash">
                                    {{ invoice.number }}
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
                            <p>
                                <a v-if="invoice.company" :href="'/companys/' + invoice.company.hash">
                                    {{ invoice.company.business_name }}
                                </a>
                                <a v-else :href="'/guests/' + invoice.guests[0].hash">
                                    {{ invoice.guests[0].name }} {{ invoice.guests[0].last_name }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>
                                $ {{ new Intl.NumberFormat("de-DE").format(invoice.value) }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1 align-self-center">
                            <p>
                                {{ calculatePaymentPercentage(invoice) }}%
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center">
                            <p>
                                {{ invoice.reservation ? 'Reserva' : 'Ingreso' }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center">
                            <p>
                                {{ invoice.created_at | formatDate }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-1 col-md-1 col-lg- align-self-center">
                            <p v-if="invoice.open"><i class="text-primary fa fa-lock-open fa-2x"></i></p>
                            <p v-else><i class="text-primary fa fa-lock fa-2x"></i></p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <a href="#" @click.prevent="removeInvoice(invoice)">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="row" v-for="room in invoice.rooms" :key="room.hash">
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center text-right text-muted">
                            <i class="fas fa-caret-right"></i>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center dont-break-out">
                            <p>{{ room.number }}</p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center dont-break-out">
                            <p>{{ room.pivot.quantity }}</p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <!-- PRICE HERE -->
                            <p>$ {{ new Intl.NumberFormat("de-DE").format(room.pivot.subvalue) }}</p>
                        </div>
                        <!-- <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>$ {{ new Intl.NumberFormat("de-DE").format(room.pivot.subvalue) }}</p>
                        </div> -->
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>$ {{ new Intl.NumberFormat("de-DE").format(room.pivot.discount) }}</p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>$ {{ new Intl.NumberFormat("de-DE").format(room.pivot.taxes) }}</p>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
                            <p>$ {{ new Intl.NumberFormat("de-DE").format(room.pivot.value) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="crud-list-items" v-else>
                <div class="crud-list-row">
                    <div class="card mt-4">
                        <div class="card-body">
                            Sin registros.
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
            this.invoices = _.first(this.hotels).invoices
        }
    },
    props: ['hotels'],
    data() {
        return {
            hotel: null, // The current hotel hash
            invoices: []
        }
    },
    methods: {
        updateInvoiceList() {
            _.map(this.hotels, (headquarter) => {
                // Filter by current hotel hash
                if (headquarter.hash == this.hotel) {
                    this.invoices = headquarter.invoices
                }
            })
        },
        getSelectedHotel() {
            return _.find(this.hotels, (headquarter) => {
                return headquarter.hash == this.hotel;
            })
        },
        calculatePaymentPercentage(invoice) {
            let percentage = 0
            let sum = 0

            if (invoice.payments.length != 0) {
                sum = _.reduce(invoice.payments, (memo, payment) => {
                    return memo + parseFloat(payment.value)
                }, 0);
            }

            percentage = (sum / parseFloat(invoice.value)) * 100

            return percentage.toFixed(2)
        },
        removeInvoice(invoice) {
            this.invoices = _.filter(this.invoices, (item) => {
                return item.hash != invoice.hash
            })
        },
        process() {
            let numbers = []

            if (this.invoices.length > 0) {
                _.map(this.invoices, (invoice) => {
                    numbers.push(invoice.number)
                })

                axios.post('/invoices/process', {
                    numbers: numbers,
                    hotel: this.hotel
                }).then(response => {
                    console.log(response);
                }).catch(e => {
                    console.log(e.response.data);
                    console.log(e.response.status);
                    console.log(e.response.headers);
                    toastr.error(
                        $t('common.try'),
                        'Error'
                    );
                });
            } else {
                toastr.error(
                    $t('common.noRecords'),
                    'Error'
                );
            }
        }
    },
    filters: {
        formatDate: function (value) {
            if (value) {
                return moment(String(value)).format('YY-MM-DD')
            }
        }
    }
};
</script>