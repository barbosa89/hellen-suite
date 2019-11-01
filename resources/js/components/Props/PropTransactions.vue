<template>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2 class="text-center mb-4">Transacciones de Utilería</h2>
            <div class="form-group mb-4">
                <select name="hotel" id="hotel" class="form-control" v-model="hotel">
                    <option v-for="hotel in hotels" :key="hotel.hash" :value="hotel.hash">
                        {{ hotel.business_name }}
                    </option>
                </select>
            </div>

            <div v-if="selecteds.length != 0">
                <div class="row">
                    <div class="col-4">
                        <h4>Descripción</h4>
                    </div>
                    <div class="col-4">
                        <h4>Cantidad</h4>
                    </div>
                    <div class="col-4">
                        <h4>Comentario</h4>
                    </div>
                </div>

                <div class="row" v-for="selected in selecteds" :key="selected.hash">
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" class="form-control without-border" name="description" id="description" required maxlength="191" placeholder="Requerido" :value="selected.description">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <input type="number" class="form-control without-border" name="quantity" id="quantity" required max="191" placeholder="Requerido">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <textarea class="form-control without-border" name="commentary" id="commentary" cols="30" rows="2" placeholder="Escriba comentario aquí"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group mb-4">
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
                        <a href="'#'" @click.prevent="addProp(prop)" class="">
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
                <button type="submit" class="btn btn-primary">Procesar</button>
                <a href="/props/index" class="btn btn-default">
                    Back
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
                quantity: 0,
                commentary: ''
            }
        },
        methods: {
            addProp(prop) {
                let contains = _.includes(this.selecteds, prop)

                if (!contains) {
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