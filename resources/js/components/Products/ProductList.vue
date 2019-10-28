<template>
    <div>
        <div class="row mb-4">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group">
                    <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="updateProductList">
                        <option v-for="hotel in hotels" :key="hotel.hash" :value="hotel.hash">
                            {{ hotel.business_name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="input-group">
                    <input class="form-control" type="search" name="query" v-model="query" placeholder="Buscar" aria-label="Search" required>
                    <div class="input-group-append">
                        <button class="input-group-text" id="btnGroupAddon">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="crud-list">
            <div class="crud-list-heading mt-2">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                        <h5>Descripción</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>Marca</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>Referencia</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>Precio</h5>
                    </div>
                    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
                        <h5>Existencia</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>Estado</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>Opciones</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" v-if="products.length != 0">
                <div class="crud-list-row" v-for="product in products" :key="product.hash">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center dont-break-out">
                            <p>
                                <a :href="'/products/' + product.hash">{{ product.description }}</a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                            <p>
                                <a :href="'/products/' + product.hash">Postobón</a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                            <p>
                                <a :href="'/products/' + product.hash">123</a>
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
                            <p>
                                <a :href="'/products/' + product.hash">2.000,00</a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <p>
                                <a :href="'/products/' + product.hash">54</a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <p class="text-primary">
                                <i class="fas" :class="product.status == '1' ? 'fa-check' : 'fa-times-circle'"></i>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <div class="dropdown">
                                <button
                                    type="button"
                                    id="dropdownMenuButton"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    class="btn btn-link"
                                    >
                                <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <div aria-labelledby="dropdownMenuButton" class="dropdown-menu dropdown-menu-right">
                                    <a
                                        :href="'/products/' + product.hash + '/increase'"
                                        class="dropdown-item"
                                        v-if="$can('products.edit')"
                                        >Incrementar stock</a>
                                    <a :href="'#'" class="dropdown-item" v-if="$can('products.edit')">Registrar pérdidas</a>
                                    <div class="dropdown-divider"></div>
                                    <a
                                        :href="'/products/' + product.hash + '/edit'"
                                        class="dropdown-item"
                                        v-if="$can('products.edit')">Editar</a>
                                    <a
                                        :href="'/products/' + product.hash + '/toggle'"
                                        class="dropdown-item"
                                        v-if="$can('products.edit')"
                                        >{{ product.status == 1 ? 'Deshabilitar' : 'Habilitar' }}</a>
                                    <a
                                        :href="'#'"
                                        :data-url="'/products/' + product.hash"
                                        data-method="DELETE"
                                        id="modal-confirm"
                                        onclick="confirmAction(this, event)"
                                        class="dropdown-item"
                                        v-if="$can('products.destroy')"
                                        >Eliminar</a>
                                </div>
                            </div>
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
            this.products = _.first(this.hotels).products
        }
    },
    props: ['hotels'],
    data() {
        return {
            hotel: null,
            products: [],
            query: ''
        }
    },
    methods: {
        updateProductList() {
            _.map(this.hotels, (headquarter) => {
                if (headquarter.hash == this.hotel) {
                    this.products = headquarter.products
                    this.query = ''
                }
            })
        }
    },
    watch: {
        query: function(current, old) {
            if (current.length == 0 || this.query.length == 0) {
                this.updateProductList()
            } else {
                if (current.length >= 3) {
                    axios.post('/products/list', {
                        query: this.query,
                        hotel: this.hotel
                    }).then(response => {
                        let products = JSON.parse(response.data.products);

                        if (products.length > 0) {
                            this.products = products
                        } else {
                            this.products = []

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