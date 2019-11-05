<template>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2 class="text-center mb-4">Transacciones de Utilería</h2>
            <div class="form-group mb-4">
                <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="resetAll">
                    <option v-for="hotel in hotels" :key="hotel.hash" :value="hotel.hash">
                        {{ hotel.business_name }}
                    </option>
                </select>
            </div>

            <div class="form-group mb-4">
                <select name="transaction" id="transaction" class="form-control" @change="resetAll">
                    <option value="1">Salida</option>
                    <option value="2">Entrada</option>
                </select>
            </div>

            <div v-if="selecteds.length != 0">
                <div class="row">
                    <div class="col-3">
                        <h4>Descripción</h4>
                    </div>
                    <div class="col-3">
                        <h4>Cantidad</h4>
                    </div>
                    <div class="col-4">
                        <h4>Comentario</h4>
                    </div>
                </div>

                <div class="row" v-for="(selected, index) in selecteds" :key="selected.hash">
                    <template v-if="!selected.editing">
                        <div class="col-3">
                            <p>
                                {{ selected.description }}
                            </p>
                        </div>
                        <div class="col-3">
                            <p>
                                {{ selected.amount }}
                            </p>
                        </div>
                        <div class="col-4">
                            <p>
                                {{ selected.commentary }}
                            </p>
                        </div>
                        <div class="col-2 text-right">
                            <button type="button" class="btn btn-link" @click.prevent="editProp(selected, index)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-link" @click.prevent="selecteds.splice(index, 1)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>

                    <template v-else>
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" class="form-control without-border" readonly :value="selected.description">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <input type="number" class="form-control without-border" name="amount" id="amount" v-model="amount" required min="1" max="999" placeholder="Requerido">
                            </div>
                        </div>
                        <div class="col-4">
                            <p v-if="!selected.editing">
                                {{ selected.commentary }}
                            </p>
                            <div class="form-group">
                                <textarea class="form-control without-border" name="commentary" id="commentary" v-model="commentary" cols="30" rows="3" placeholder="Escriba comentario aquí"></textarea>
                            </div>
                        </div>
                        <div class="col-2 text-right">
                            <button type="button" class="btn btn-link" @click.prevent="saveProp(selected, index)">
                                <i class="fas fa-save"></i>
                            </button>
                            <button type="button" class="btn btn-link" @click.prevent="cancelEditing()">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="input-group mb-4 mt-4">
                <input class="form-control" type="search" name="query" v-model="query" placeholder="Buscar" aria-label="Search" required>
                <div class="input-group-append">
                    <button class="input-group-text" id="btnGroupAddon">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="crud-list" v-if="props.length != 0">
                <div class="crud-list-heading mt-2">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <h5>Descripción</h5>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <h5>Cantidad actual</h5>
                        </div>
                    </div>
                </div>
                <div class="crud-list-items">
                    <div class="crud-list-row" v-for="prop in props" :key="prop.hash">
                        <a href="'#'" @click.prevent="addProp(prop)" class="crud-item-link">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 align-self-center">
                                    <p>
                                        <a :href="'/props/' + prop.hash">
                                            {{ prop.description }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 align-self-center">
                                    <p class="text-primary">
                                        {{ prop.quantity }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" class="btn btn-primary">Procesar</button>
                <a href="/props/index" class="btn btn-default">
                    {{ $t('common.back') }}
                </a>
            </div>
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
                props: [],
                query: '',
                selecteds: [],
                hash: '',
                amount: 0,
                commentary: ''
            }
        },
        methods: {
            resetAll() {
                this.props = [],
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

                    this.props = _.filter(this.props, (item) => {
                        return item.hash != prop.hash
                    })

                    if (this.props.length == 0) {
                        this.query = ''
                    }
                } else {
                    toastr.info(
                        'Ya has agregado este elemento',
                        'Lo siento'
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
                selected.amount = this.amount
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
            }
        },
        watch: {
            query: function(current, old) {
                if (current.length == 0 || this.query.length == 0) {
                    this.props = []
                } else {
                    if (current.length >= 3) {
                        axios.post('/props/search', {
                            query: this.query,
                            hotel: this.hotel
                        }).then(response => {
                            let props = JSON.parse(response.data.props);

                            if (props.length > 0) {
                                this.props = props
                            } else {
                                this.props = []

                                toastr.info(
                                    'La búsqueda no arrojó resultados',
                                    'Lo siento'
                                );
                            }
                        }).catch(e => {
                            toastr.error(
                                'Intenta más tarde otra vez',
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
    .crud-list-row:hover {
        background-color: #efefef !important;
    }

    a.crud-item-link:hover,
    a.crud-item-link:active,
    a.crud-item-link:link,
    a.crud-item-link:visited {
        text-decoration: none;
    }
</style>