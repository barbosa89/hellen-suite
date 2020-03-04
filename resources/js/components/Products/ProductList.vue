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
                    <input class="form-control" type="search" name="query" v-model="query" :placeholder='$t("common.search")' aria-label="Search" required>
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
                        <h5>{{ $t('common.description') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>{{ $t('common.brand') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>{{ $t('common.reference') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>{{ $t('common.price') }}</h5>
                    </div>
                    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
                        <h5>{{ $t('common.existence') }}</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>{{ $t('common.status') }}</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                        <h5>{{ $t('common.options') }}</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" v-if="products.length != 0">
                <div class="crud-list-row" v-for="product in products" :key="product.hash">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center dont-break-out">
                            <p>
                                <a v-if="$can('products.show')" :href="'/products/' + product.hash">
                                    {{ product.description }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                            <p>
                                <a v-if="$can('products.show')" :href="'/products/' + product.hash">
                                    {{ product.brand || 'Sin datos' }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                            <p>
                                <a v-if="$can('products.show')" :href="'/products/' + product.hash">
                                    {{ product.reference || 'Sin datos' }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
                            <p>
                                <a v-if="$can('products.show')" :href="'/products/' + product.hash">
                                    {{ new Intl.NumberFormat("de-DE").format(product.price) }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                            <p>
                                <a v-if="$can('products.show')" :href="'/products/' + product.hash">
                                    {{ product.quantity }}
                                </a>
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
                                        v-if="$can('products.edit')">
                                        {{ $t('products.increase') }}
                                    </a>
                                    <a :href="'/products/' + product.hash + '/sales'"
                                        class="dropdown-item"
                                        v-if="$can('sales.index')">
                                        {{ $t('sales.sales') }}
                                    </a>
                                    <a :href="'/products/' + product.hash + '/sales/create'"
                                        class="dropdown-item"
                                        v-if="$can('sales.create')">
                                        {{ $t('sales.register') }}
                                    </a>
                                    <a :href="'#'"
                                        class="dropdown-item"
                                        v-if="$can('products.edit')">
                                        {{ $t('products.losses') }}
                                    </a>
                                    <a
                                        :href="'/products/' + product.hash + '/edit'"
                                        class="dropdown-item"
                                        v-if="$can('products.edit')">
                                        {{ $t('common.edit') }}
                                    </a>
                                    <a
                                        :href="'/products/' + product.hash + '/toggle'"
                                        class="dropdown-item"
                                        v-if="$can('products.edit')">
                                        {{ product.status == 1 ? $t('common.disable') : $t('common.disable') }}
                                    </a>
                                    <a
                                        :href="'#'"
                                        :data-url="'/products/' + product.hash"
                                        data-method="DELETE"
                                        id="modal-confirm"
                                        onclick="confirmAction(this, event)"
                                        class="dropdown-item"
                                        v-if="$can('products.destroy')">
                                        {{ $t('common.delete.item') }}
                                    </a>
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
            _.map(this.hotels, (headquarters) => {
                if (headquarters.hash == this.hotel) {
                    this.products = headquarters.products
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
