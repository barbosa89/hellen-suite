<template>
    <div>
        <div class="row mb-4">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group">
                    <select name="hotel" id="hotel" class="form-control" v-model="hotel" @change="updateServiceList">
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
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <h5>Descripción</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>Cantidad</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <h5>Estado</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                        <h5>Opciones</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" v-if="props.length != 0">
                <div class="crud-list-row" v-for="prop in props" :key="prop.hash">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 align-self-center">
                            <p>
                                <a :href="'/props/' + prop.hash">
                                    {{ prop.description }}
                                </a>
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
                            <p class="text-primary">
                                {{ prop.quantity }}
                            </p>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
                            <p class="text-primary">
                                <i class="fas" :class="prop.status == '1' ? 'fa-check' : 'fa-times-circle'"></i>
                            </p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                            <div class="dropdown">
                                <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-link">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>

                                <div aria-labelledby="dropdownMenuButton" class="dropdown-menu dropdown-menu-right">
                                    <a :href="'/props/' + prop.hash + '/edit'" class="dropdown-item">Editar</a>
                                    <a :href="'/props/' + prop.hash + '/toggle'" class="dropdown-item">
                                        {{ prop.status == 1 ? 'Deshabilitar' : 'Habilitar' }}
                                    </a>
                                    <a href="#" :data-url="'/props/' + prop.hash" data-method="DELETE" id="modal-confirm" onclick="confirmAction(this, event)" class="dropdown-item">
                                    Eliminar
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
        updateServiceList() {
            _.map(this.hotels, (headquarter) => {
                if (headquarter.hash == this.hotel) {
                    this.props = headquarter.props
                    this.query = ''
                }
            })
        }
    },
    watch: {
        query: function(current, old) {
            if (current.length == 0 || this.query.length == 0) {
                this.updateServiceList()
            } else {
                if (current.length >= 3) {
                    axios.post('/props/list', {
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
                        console.log(e.response.data);
                        console.log(e.response.status);
                        console.log(e.response.headers);
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