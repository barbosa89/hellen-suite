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
                        <div class="btn-group pull-left" role="group" id="filters">
                            <!-- <div v-if="$can('rooms.create')">Create rooms.</div> -->
                            <button type="button" class="btn btn-default pressed" id="all" @click.prevent="showAll" title="Todo">
                                <i class="fa fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="available" @click.prevent="showAvailable" title="Disponible">
                                <i class="fa fa-check-circle"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="occupied" @click.prevent="showOccupied" title="Ocupado">
                                <i class="fa fa-tags"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="maintenance" @click.prevent="showMaintenance" title="En limpieza">
                                <i class="fa fa-broom"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="cleaning" @click.prevent="showCleaning" title="En mantenimiento">
                                <i class="fa fa-wrench"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="disabled" @click.prevent="showDisabled" title="Inhabilitado">
                                <i class="fa fa-lock"></i>
                            </button>
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
                        @dblclick="pushSelected(room)"
                        :class="room.selected == true ? 'selected-room' : ''"
                        data-toggle="tooltip" data-placement="top" :title="room.description">
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
                        <h3 class="text-center noselect">{{ room.number }}</h3>
                        <p class="text-center">
                            <small class="d-block noselect">$ {{ new Intl.NumberFormat("de-DE").format(room.price) }}</small>
                        </p>
                        <p class="text-center mb-2">
                            <i class="text-info fa" v-if="room.status == '0'" :class="room.status == '0' ? 'fa-tags' : ''"></i>
                            <i class="text-info fa" v-else-if="room.status == '1'" :class="room.status == '1' ? 'fa-check' : ''"></i>
                            <i class="text-info fa" v-else-if="room.status == '2'" :class="room.status == '2' ? 'fa-broom' : ''"></i>
                            <i class="text-info fa" v-else-if="room.status == '4'" :class="room.status == '4' ? 'fa-wrench' : ''"></i>
                            <i class="text-info fa" v-else :class="room.status == '3' ? 'fa-lock' : ''"></i>
                            <span class="d-inline-block">{{ room.capacity }}</span>
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
            if (this.hotels.length > 0) {
                this.selectedHotel = _.first(this.hotels).hash
                this.rooms = _.first(this.hotels).rooms

                // Add custom property selected
                this.rooms = _.each(this.rooms, function (room) {
                    room.selected = false
                    room.price = parseFloat(room.price) + (parseFloat(room.price) * parseFloat(room.tax))
                })

                this.filteredRooms = this.rooms
            }
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
            pressed(event) {
                let buttons = document.getElementById('filters')
                let ArrBtn = Array.from(buttons.children)

                ArrBtn.forEach(function (button, index) {
                    button.classList.remove('pressed')
                })

                let tag = event.target.localName
                let id = tag == 'i' ? event.target.parentNode.id : event.target.id

                let button = document.getElementById(id)
                button.classList.add('pressed')
            },
            updateRoomList() {
                _.map(this.hotels, (hotel) => {
                    if (hotel.hash == this.selectedHotel) {
                        this.filteredRooms = hotel.rooms
                        this.rooms = hotel.rooms
                    }
                })
            },
            showAll(event) {
                this.filteredRooms = this.rooms
                this.pressed(event)
            },
            showAvailable(event) {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '1'
                })

                this.pressed(event)
            },
            showOccupied(event) {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '0'
                })

                this.pressed(event)
            },
            showMaintenance(event) {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '2'
                })

                this.pressed(event)
            },
            showDisabled(event) {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '3'
                })

                this.pressed(event)
            },
            showCleaning(event) {
                this.filteredRooms = _.filter(this.rooms, (room) => {
                    return room.status == '4'
                })

                this.pressed(event)
            },
            pushSelected(room) {
                if (room.status == '1') {
                    let contains = _.includes(this.selectedRooms, room)

                    if (contains) {
                        this.selectedRooms = _.filter(this.selectedRooms, (selected) => {
                            return selected.hash != room.hash
                        })

                        room.selected = false
                    } else {
                        this.selectedRooms.push(room)
                        room.selected = true
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
                _.each(this.selectedRooms, function (room) {
                    room.selected = false
                })

                this.selectedRooms = []
            },
            show(text, data) {
                let url = '/rooms/' + data.room.hash;
                window.location.href = url;
            },
            assign(text, data) {
                if (data.room.status == '1') {
                    this.pushSelected(data.room);
                    this.send(this.selectedHotel, [data.room])
                } else {
                    toastr.info(
                        'No puedes agregar esta habitación',
                        'Acción no permitida'
                    );
                }
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

                    window.location.href = response.data.redirect;
                }).catch(e => {
                    toastr.error(
                        'Intenta más tarde otra vez',
                        'Error'
                    );
                });
            }
        },
    }
</script>

<style scoped>
    .pressed {
        background-color: #c7c7c7;
    }

    #filters > .btn.active.focus,
    .btn.active:focus,
    .btn.focus,
    .btn.focus:active,
    .btn:active:focus,
    .btn:focus {
        outline: 0 !important;
        outline-offset: 0  !important;
        background-image: none  !important;
        -webkit-box-shadow: none !important;
        box-shadow: none  !important;
    }
</style>
