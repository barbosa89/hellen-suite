<template>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 without-padding">
                    <div class="form-group">
                        <select name="hotel" id="hotel" class="form-control" v-model="selectedHotel" @change="updateRoomList">
                            <option v-for="(hotel, index) in hotels" :key="hotel.hash" :selected="index === 0" :value="hotel.hash">
                                {{ hotel.business_name }}
                            </option>
                        </select>
                    </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="row">
                    <div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8 col-xl-8">
                        <div class="btn-group pull-left" role="group" aria-label="Basic example">
                            <!-- <div v-if="$can('rooms.create')">Create rooms.</div> -->
                            <button type="button" class="btn btn-default" @click.prevent="showAll" title="Todo"><i class="fa fa-align-justify"></i></button>
                            <button type="button" class="btn btn-default" @click.prevent="showAvailable" title="Disponible"><i class="fa fa-check-circle"></i></button>
                            <button type="button" class="btn btn-default" @click.prevent="showOccupied" title="Ocupado"><i class="fa fa-tags"></i></button>
                            <button type="button" class="btn btn-default" @click.prevent="showMaintenance" title="En limpieza"><i class="fa fa-broom"></i></button>
                            <button type="button" class="btn btn-default" @click.prevent="showCleaning" title="En mantenimiento"><i class="fa fa-wrench"></i></button>
                            <button type="button" class="btn btn-default" @click.prevent="showDisabled" title="Inhabilitado"><i class="fa fa-lock"></i></button>
                        </div>
                    </div>
                    <div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-right">
                        <div v-show="selectedRooms.length > 0" class="btn-group pull-right" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-default" title="Asignar" @click.prevent="pool">{{ selectedRooms.length }} <i class="fa fa-key"></i></button>
                            <button type="button" class="btn btn-default" title="Vaciar" @click.prevent="clear"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="filteredRooms.length != 0">
            <div class="col-12" v-for="(chunk, index) in chunkedItems" :key="index">
                <div class="row">
                    <div class="col-12 col-sm-4 col-md-2 col-lg-2 col-xl-2 room"
                        v-for="room in chunk" :key="room.id"
                        @contextmenu.prevent="$refs.menu.open($event, { room })"
                        @dblclick="pushSelected(room)">
                        <div class="row">
                            <div class="col-12 without-padding">
                                <p class="text-right">
                                    <a href="#" class="text-info context-option d-none d-md-inline d-lg-inline d-xl-inline" @click.stop="$refs.menu.open($event, { room })">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </a>
                                    <a href="#" class="text-info context-option d-inline d-md-none d-lg-none d-xl-none" @click.prevent="pushSelected(room)">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <h3 class="text-center">{{ room.number }}</h3>
                        <p class="text-center">
                            <small class="d-block">$ {{ new Intl.NumberFormat("de-DE").format(room.price) }}</small>
                        </p>
                        <p class="text-center mb-2">
                            <i class="fa text-info" :class="room.status == '1' ? 'fa-check' : 'fa-times-circle'"></i>
                            <span>{{ room.capacity }} </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-else>
            <div class="col-12">
                <div class="card mt-4">
                    <div class="card-body">
                        Sin registros.
                    </div>
                </div>
            </div>
        </div>

        <vue-context ref="menu">
            <template slot-scope="child">
                <li>
                    <a href="#" @click.prevent="assign($event.target.innerText, child.data)">
                        Asignar
                    </a>
                </li>
                <li>
                    <a href="#" @click.prevent="select($event.target.innerText, child.data)">
                        Seleccionar
                    </a>
                </li>
                <li>
                    <a href="#" @click.prevent="show($event.target.innerText, child.data)">
                        Ver detalles
                    </a>
                </li>
            </template>
        </vue-context>
    </div>
</template>

<script>
    import { VueContext } from 'vue-context';

    export default {
        mounted() {
            // console.log('Component mounted.')
            this.selectedHotel = _.first(this.hotels).hash
            this.filteredRooms = _.first(this.hotels).rooms;
        },
        data() {
            return {
                selectedHotel: '',
                rooms: [],
                filteredRooms: [],
                selectedRooms: [],
                showStatus: false
            }
        },
        props: ['hotels'],
        computed: {
            chunkedItems() {
                return _.chunk(this.filteredRooms, 6)
            }
        },
        components: {
            VueContext
        },
        methods: {
            updateRoomList() {
                _.map(this.hotels, (hotel) => {
                    if (hotel.hash == this.selectedHotel) {
                        this.filteredRooms = hotel.rooms
                        this.rooms = hotel.rooms
                    }
                })
            },
            showAll() {
                this.filteredRooms = this.rooms
            },
            showAvailable() {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '1'
                })
            },
            showOccupied() {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '0'
                })
            },
            showMaintenance() {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '2'
                })
            },
            showDisabled() {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '3'
                })
            },
            showCleaning() {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '4'
                })
            },
            pushSelected(room) {
                if (room.status == '1') {
                    let contains = _.includes(this.selectedRooms, room)

                    if (!contains) {
                        this.selectedRooms.push(room)
                    }
                } else {
                    toastr.info(
                        'No puedes agregar esta habitación',
                        'Acción no permitida'
                    );
                }
            },
            select(text, data) {
                this.pushSelected(data.room);
            },
            clear() {
                this.selectedRooms = []
            },
            show(text, data) {
                let url = '/rooms/' + data.room.hash;
                window.location.href = url;
            },
            assign(text, data) {
                this.pushSelected(data.room);
                this.send(this.selectedHotel, [data.room])
            },
            pool() {
                this.send(this.selectedHotel, this.selectedRooms)
            },
            send(hotel, rooms) {
                axios.post('/invoices/multiple', {
                    hotel: hotel,
                    rooms: rooms
                }).then(response => {
                    this.selectedRooms = []
                    console.log(response);
                    // let id = responde.id;
                    // let url = window.location.host + '/invoices/' + id;
                    // window.location.href = url;
                }).catch(e => {
                    console.log(e);
                });
            }
        },
    }
</script>
