<template>
    <div>
        <div class="row mb-4">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group">
                    <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="updatePropList">
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
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <h5>{{ $t('common.description') }}</h5>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <h5>{{ $t('common.quantity') }}</h5>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <h5>{{ $t('common.value') }}</h5>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <h5>{{ $t('common.options') }}</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" v-if="props.length != 0">
                <div class="crud-list-row" v-for="prop in props" :key="prop.hash">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                            <p>
                                <a :href="'/props/' + prop.hash">
                                    {{ prop.description }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                            <p class="text-primary">
                                {{ prop.quantity }}
                            </p>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                            <p class="text-primary">
                                {{ new Intl.NumberFormat("de-DE").format(prop.price) }}
                            </p>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                            <div class="dropdown">
                                <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-link">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>

                                <div aria-labelledby="dropdownMenuButton" class="dropdown-menu dropdown-menu-right">
                                    <a v-if="$can('props.edit')" :href="'/props/' + prop.hash + '/edit'" class="dropdown-item">
                                        {{ $t('common.edit') }}
                                    </a>
                                    <a v-if="$can('props.destroy')" href="#" :data-url="'/props/' + prop.hash" data-method="DELETE" id="modal-confirm" onclick="confirmAction(this, event)" class="dropdown-item">
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
            this.props = _.first(this.hotels).props
        }
    },
    props: ['hotels'],
    data() {
        return {
            hotel: null,
            props: [],
            query: ''
        }
    },
    methods: {
        updatePropList() {
            _.map(this.hotels, (headquarters) => {
                if (headquarters.hash == this.hotel) {
                    this.props = headquarters.props
                    this.query = ''
                }
            })
        }
    },
    watch: {
        query: function(current, old) {
            if (current.length == 0 || this.query.length == 0) {
                this.updatePropList()
            } else {
                if (current.length >= 3) {
                    axios.get('/props/search', {
                        params: {
                            query: this.query,
                            hotel: this.hotel
                        }
                    }).then(response => {
                        let props = JSON.parse(response.data.props);

                        if (props.length > 0) {
                            this.props = props
                        } else {
                            this.props = []

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
