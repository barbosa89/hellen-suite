<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light app-nav border border-top-0 border-right-0 border-left-0">
            <a v-if="$can('products.index')" href="/products" class="navbar-brand text-muted">
                {{ $t('products.title') }}
            </a>

            <button type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navbarNavDropdown" class="collapse navbar-collapse">
                <form class="form-inline my-2 my-lg-0" method="get">
                    <div class="ui search focus">
                        <div class="ui left icon input">
                            <div class="input-group">
                                <input class="form-control" type="search" name="query" v-model="query" :placeholder='$t("common.search")' aria-label="Search" required>
                            </div>
                        </div>
                        <transition name="fade">
                            <div class="results transition visible" v-if="products.length != 0" style="display: block !important;">
                                <a class="result" v-for="prop in products" :key="prop.hash" @click.prevent="addProp(prop)">
                                    <div class="content">
                                        <div class="title">{{ prop.description }}</div>
                                        <div class="description">Cantidad en existencia: {{ prop.quantity }}</div>
                                    </div>
                                </a>
                            </div>
                        </transition>
                    </div>
                </form>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a v-if="$can('products.create')" href="/products/create" class="nav-link">
                            {{ $t('common.new') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a v-if="$can('products.index')" href="/products" class="nav-link">
                            {{ $t('common.back') }}
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <h2 class="text-center mb-4 mt-4">{{ $t('products.transactions') }}</h2>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group mb-4">
                    <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="resetAll">
                        <option v-for="hotel in hotels" :key="hotel.hash" :value="hotel.hash">
                            {{ hotel.business_name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group mb-4">
                    <select name="type" id="type" v-model="type" class="form-control">
                        <option :value="null" disabled selected>{{ $t('transactions.select.type') }}</option>
                        <option value="discharge">{{ $t('transactions.discharge') }}</option>
                        <option value="entry">{{ $t('transactions.entry') }}</option>
                        <option value="sales">{{ $t('transactions.sales') }}</option>
                        <option value="losses">{{ $t('transactions.losses') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="crud-list" v-if="selecteds.length != 0">
            <div class="crud-list-heading mt-2">
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <h5>{{ $t('common.description') }}</h5>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <h5>Cantidad</h5>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <h5>Comentario</h5>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <h5>{{ $t('common.options') }}</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items">
                <div class="crud-list-row" v-for="(selected, index) in selecteds" :key="selected.hash">
                    <template v-if="!selected.editing">
                        <div class="row">
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                <p>
                                    {{ selected.description }}
                                </p>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                <p>
                                    {{ selected.amount }}
                                </p>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                <p>
                                    {{ selected.commentary }}
                                </p>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                <button type="button" class="btn btn-link" @click.prevent="editProp(selected, index)">
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
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <input type="text" class="form-control without-border" readonly :value="selected.description">
                                </div>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <input type="number" class="form-control without-border" name="amount" id="amount" v-model="amount" required min="1" :max="selected.quantity" placeholder="Requerido">
                                </div>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <textarea class="form-control without-border" name="commentary" id="commentary" v-model="commentary" cols="30" rows="3" placeholder="Escriba comentario aquí"></textarea>
                                </div>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <button type="button" class="btn btn-link" @click.prevent="saveProp(selected, index)">
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
            <button type="button" class="btn btn-primary" @click.prevent="process">Procesar</button>
            <a class="btn btn-default" @click.prevent="resetAll">
                Borrar todo
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            if (this.hotels.length > 0) {
                this.hotel = _.first(this.hotels).hash
            }
        },
        props: ['hotels'],
        data() {
            return {
                hotel: null,
                type: null,
                products: [],
                query: '',
                selecteds: [],
                hash: '',
                amount: 0,
                commentary: ''
            }
        },
        methods: {
            resetAll() {
                this.products = [],
                this.query = '',
                this.selecteds = [],
                this.amount = 0,
                this.commentary = ''
            },
            addProp(prop) {
                if (!this.exists(prop)) {
                    this.cancelEditing()

                    prop.amount = 0
                    prop.commentary = ''
                    prop.editing = true

                    this.selecteds.push(prop)

                    this.products = _.filter(this.products, (item) => {
                        return item.hash != prop.hash
                    })

                    if (this.products.length == 0) {
                        this.query = ''
                    }
                } else {
                    toastr.info(
                        'Ya has agregado este elemento',
                        this.$root.$t('common.sorry')
                    );
                }
            },
            exists(prop) {
                var exists =[]

                _.map(this.selecteds, selected => {
                    if (selected.hash == prop.hash) {
                        exists.push(selected)
                    }
                })

                return exists.length != 0
            },
            editProp(selected, index) {
                this.cancelEditing()

                selected.editing = true

                this.$set(this.selecteds, index, selected);

                this.amount = selected.amount
                this.commentary = selected.commentary
            },
            saveProp(selected, index) {
                selected.commentary = this.commentary

                if (this.amount > selected.quantity) {
                    selected.amount = selected.quantity
                } else {
                    selected.amount = this.amount
                }

                selected.editing = false

                this.$set(this.selecteds, index, selected);

                this.amount = 0
                this.commentary = ''
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
                this.commentary = ''
            },
            process() {
                if (this.selecteds.length > 0 && this.validate()) {
                    axios.post('/products/transactions', {
                        products: this.selecteds,
                        hotel: this.hotel,
                        type: this.type
                    }).then(response => {
                        this.resetAll()
                        let msg = ''

                        if (parseInt(response.data.request) == parseInt(response.data.processed)) {
                            msg = "Todos los ítems enviados fueron procesados"
                        } else {
                            msg = response.data.processed + "/" + response.data.request + " ítems procesados"
                        }

                        toastr.success(
                            msg,
                            'Genial'
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
                        'Aún no puedes procesar la transacción',
                        this.$root.$t('common.sorry')
                    );
                }
            },
            validate() {
                let errors = 0

                if (!this.hotel) {
                    errors++
                }

                if (!this.type) {
                    errors++
                    document.getElementById('type').classList.add('is-invalid')
                } else {
                    document.getElementById('type').classList.remove('is-invalid')
                }

                this.selecteds.forEach(selected => {
                    if (selected.amount == 0 || selected.commentary == '') {
                        errors++
                    }
                })

                return errors == 0;
            }
        },
        watch: {
            query: function(current, old) {
                if (current.length == 0 || this.query.length == 0) {
                    this.products = []
                } else {
                    if (current.length >= 3) {
                        axios.post('/products/search', {
                            query: this.query,
                            hotel: this.hotel
                        }).then(response => {
                            let products = JSON.parse(response.data.products);

                            if (products.length > 0) {
                                this.products = products
                            } else {
                                this.products = []

                                toastr.info(
                                    this.$root.$t('common.without.results'),
                                    this.$root.$t('common.sorry')
                                );
                            }
                        }).catch(e => {
                            toastr.error(
                                this.$root.$t('common.try'),
                                'Error'
                            );
                        });
                    }
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