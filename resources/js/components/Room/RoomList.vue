<template>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-6 without-padding">
                <div class="btn-group pull-left" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-default" @click.prevent="showAll" title="Todo"><i class="fa fa-align-justify"></i></button>
                    <button type="button" class="btn btn-default" @click.prevent="showAvailable" title="Disponible"><i class="fa fa-check-circle"></i></button>
                    <button type="button" class="btn btn-default" @click.prevent="showOccupied" title="Ocupado"><i class="fa fa-tags"></i></button>
                    <button type="button" class="btn btn-default" @click.prevent="showMaintenance" title="En limpieza"><i class="fa fa-paint-brush"></i></button>
                    <button type="button" class="btn btn-default" @click.prevent="showCleaning" title="En mantenimiento"><i class="fa fa-wrench"></i></button>
                    <button type="button" class="btn btn-default" @click.prevent="showDisabled" title="Inhabilitado"><i class="fa fa-lock"></i></button>
                </div>
            </div>
            <div class="col-6 without-padding" v-show="selected.length > 0">
                <div class="btn-group pull-right" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-default" title="Asignar" @click.prevent="pool">{{ selected.length }} <i class="fa fa-key"></i></button>
                </div>
            </div>
        </div>
        <div class="row" v-if="filtered.length != 0">
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
                            <small>$ {{ new Intl.NumberFormat("de-DE").format(room.price) }}</small>
                        </p>
                        <p class="text-center mb-2">
                            <i class="fa fa-check text-info"></i>
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
            // console.log(this.rooms)
            this.filtered = this.rooms;
        },
        data() {
            return {
                filtered: [],
                selected: [],
                showStatus: false
            }
        },
        props: ['rooms'],
        computed: {
            chunkedItems() {
                return _.chunk(this.filtered, 6)
            }
        },
        components: {
            VueContext
        },
        methods: {
            showAll() {
                this.filtered = this.rooms
            },
            showAvailable() {
                this.filtered = _.filter(this.rooms, (room) => {
                    return room.status == '1'
                })
            },
            showOccupied() {
                this.filtered = _.filter(this.rooms, (room) => {
                    return room.status == '0'
                })
            },
            showMaintenance() {
                this.filtered = _.filter(this.rooms, (room) => {
                    return room.status == '2'
                })
            },
            showDisabled() {
                this.filtered = _.filter(this.rooms, (room) => {
                    return room.status == '3'
                })
            },
            showCleaning() {
                this.filtered = _.filter(this.rooms, (room) => {
                    return room.status == '4'
                })
            },
            pushSelected(room) {
                let contains = _.includes(this.selected, room)

                if (!contains) {
                    this.selected.push(room)
                }
            },
            select(text, data) {
                this.pushSelected(data.room);
            },
            show(text, data) {
                let url = '/rooms/list/' + data.room.id;
                window.location.href = url;
            },
            assign(text, data) {
                let url = '/rooms/list/assign/' + data.room.id;
                window.location.href = url;
            },
            pool() {
                axios.post('/rooms/pool', {
                    rooms: this.selected
                }).then(response => {
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
