<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light app-nav border border-top-0 border-right-0 border-left-0">
            <a :href="this.module_uri" class="navbar-brand text-muted">
                {{ this.module_name }}
            </a>

            <button type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navbarNavDropdown" class="collapse navbar-collapse">
                <template v-if="this.hotel.length != 0 && this.type.length != 0">
                    <transaction-live-search :uri="this.search_uri" :hotel="this.hotel" @selectResult="add"></transaction-live-search>
                </template>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a :href="this.module_uri" class="nav-link">
                            {{ $t('common.back') }}
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <transaction-selects
            :title="this.title"
            :hotels="this.hotels"
            @selectHotel="hotel = $event"
            @selectType="type = $event">
        </transaction-selects>

        <div class="crud-list" v-if="selecteds.length != 0">
            <div class="crud-list-heading mt-2">
                <div class="row">
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        <h5>{{ $t('common.description') }}</h5>
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        <h5>{{ $t('common.quantity') }}</h5>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <h5>{{ $t('common.options') }}</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items">
                <div class="crud-list-row" v-for="(selected, index) in selecteds" :key="selected.hash">
                    <template v-if="!selected.editing">
                        <div class="row">
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 align-self-center">
                                <p>
                                    {{ selected.description }}
                                </p>
                            </div>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 align-self-center">
                                <p>
                                    {{ selected.amount }}
                                </p>
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                <button type="button" class="btn btn-link" @click.prevent="edit(selected, index)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-link" @click.prevent="selecteds.splice(index, 1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="row">
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <div class="form-group">
                                    <input type="text" class="form-control without-border" readonly :value="selected.description">
                                </div>
                            </div>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <div class="form-group">
                                    <input type="number" class="form-control without-border" name="amount" id="amount" v-model="amount" required min="1" :max="selected.quantity" placeholder="Requerido">
                                </div>
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <button type="button" class="btn btn-link" @click.prevent="save(selected, index)">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button type="button" class="btn btn-link" @click.prevent="cancelEditing()">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="mt-4" v-if="selecteds.length != 0">
            <button type="button" class="btn btn-primary" @click.prevent="process">
                {{ $t('common.process') }}
            </button>
            <a class="btn btn-default" @click.prevent="resetAll">
                {{ $t('common.delete.all') }}
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['hotels'],
        data() {
            return {
                errors: [],
                info: [],
                hotel: '',
                type: '',
                selecteds: [],
                hash: '',
                amount: 0,
                process_uri: '',
                search_uri: '',
                module_uri: '',
                title: 'Default',
                module_name: 'Default'
            }
        },
        methods: {
            resetAll() {
                this.query = ''
                this.selecteds = []
                this.amount = 0
            },
            add(element) {
                this.exists(element)
                this.checkStock(element)

                if (this.info.length == 0) {
                    this.cancelEditing()

                    element.amount = 0
                    element.editing = true

                    this.selecteds.push(element)
                }
            },
            exists(element) {
                _.map(this.selecteds, selected => {
                    if (selected.hash == element.hash) {
                        this.info.push(this.$root.$t('transactions.element.exists'))
                    }
                })
            },
            edit(selected, index) {
                this.cancelEditing()

                selected.editing = true

                this.$set(this.selecteds, index, selected);

                this.amount = selected.amount
            },
            save(selected, index) {
                this.checkAmount()

                if (this.info.length == 0) {
                    if (this.amount > selected.quantity) {
                        selected.amount = selected.quantity
                    } else {
                        selected.amount = this.amount
                    }

                    selected.editing = false

                    this.$set(this.selecteds, index, selected);

                    this.amount = 0
                }
            },
            cancelEditing() {
                if (this.selecteds.length > 0) {
                    let selectList = this.selecteds;
                    this.selecteds = []
                    this.selecteds = _.each(selectList, function (item) {
                        item.editing = false
                    })
                }

                this.amount = 0
            },
            process() {
                if (this.selecteds.length > 0 && this.errors.length == 0) {
                    axios.post(this.process_uri, {
                        elements: this.selecteds,
                        hotel: this.hotel,
                        type: this.type
                    }).then(response => {
                        this.resetAll()
                        // let msg = ''

                        // if (parseInt(response.data.request) == parseInt(response.data.processed)) {
                        //     msg = "Todos los ítems enviados fueron procesados"
                        // } else {
                        //     msg = response.data.processed + "/" + response.data.request + " ítems procesados"
                        // }
                        console.log(response);

                        toastr.success(
                            'Ok',
                            this.$root.$t('common.great')
                        );
                    }).catch(e => {
                        if (e.response) {
                            console.log(e.response.data);
                            console.log(e.response.status);
                            console.log(e.response.headers);
                        } else {
                            console.log(e);
                        }
                        toastr.error(
                            this.$root.$t('common.try'),
                            'Error'
                        );
                    });
                } else {
                    toastr.info(
                        this.$root.$t('transactions.has.errors'),
                        this.$root.$t('common.sorry')
                    );
                }
            },
            validate() {
                if (!this.hotel) {
                    this.errors.push(this.$root.$t('transactions.choose.hotel'))
                }

                if (!this.type) {
                    this.errors.push(this.$root.$t('transactions.choose.type'))
                }

                this.selecteds.forEach(selected => {
                    if (selected.amount == 0) {
                        this.errors.push(this.$root.$t('transactions.amount.zero'))
                    }
                })
            },
            checkStock(element) {
                if (this.type !== 'entry') {
                    if (parseInt(element.quantity) === 0) {
                        this.info.push(this.$root.$t('transactions.no.stock'))
                    }
                }
            },
            checkAmount() {
                if (parseInt(this.amount) <= 0 || this.amount == '') {
                    this.info.push(this.$root.$t('transactions.amount.zero'))
                }
            }
        },
        watch: {
            hotel: function (current, old) {
                this.resetAll()
            },
            type: function (current, old) {
                this.resetAll()
            },
            errors: function (current, old) {
                if (current.length > 0) {
                    current.forEach(error => {
                        toastr.error(
                            error,
                            this.$root.$t('common.sorry')
                        );
                    })

                    this.errors = []
                }
            },
            info: function (current, old) {
                if (current.length > 0) {
                    current.forEach(info => {
                        toastr.info(
                            info,
                            this.$root.$t('common.sorry')
                        );
                    })

                    this.info = []
                }
            }
        },
    };
</script>

<style scoped>
    a.crud-item-link:hover,
    a.crud-item-link:active,
    a.crud-item-link:link,
    a.crud-item-link:visited {
        text-decoration: none;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
        opacity: 0;
    }
</style>