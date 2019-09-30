<template>
    <div class="container">
        <div class="row mb-4">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-default" @click.prevent="showAll">Todo</button>
                <button type="button" class="btn btn-default" @click.prevent="showAvailable">Disponible</button>
                <button type="button" class="btn btn-default" @click.prevent="showOccupied">Ocupado</button>
                <button type="button" class="btn btn-default" @click.prevent="showMaintenance">Mantenimiento</button>
                <button type="button" class="btn btn-default" @click.prevent="showCleaning">Remodelación</button>
                <button type="button" class="btn btn-default" @click.prevent="showDisabled">Inhabilitada</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12" v-for="(chunk, index) in chunkedItems" :key="index">
                <div class="row">
                    <div class="col-12 col-sm-4 col-md-2 col-lg-2 col-xl-2 room"
                        v-for="room in chunk" :key="room.id"
                        @contextmenu.prevent="$refs.menu.open($event, { room })"
                        @dblclick="pushSelected(room)"
                        @pressup="pushSelected">
                        <div class="row">
                            <div class="col-12 without-padding">
                                <p class="text-right">
                                    <a href="#" class="text-info context-option" @click.stop="$refs.menu.open($event, { room })">
                                        <i class="fa fa-ellipsis-v"></i>
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

        <vue-context ref="menu">
            <template slot-scope="child">
                <li>
                    <a href="#" @click.prevent="onClick($event.target.innerText, child.data)">
                        Option 1
                    </a>
                </li>
                <li>
                    <a href="#" @click.prevent="onClick($event.target.innerText, child.data)">
                        Option 2
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
            onClick (text, data) {
                alert(`You clicked ${text}!`);
                console.log(data.room);
            },
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
                console.log(this.selected)
            }

        },
    }
</script>
