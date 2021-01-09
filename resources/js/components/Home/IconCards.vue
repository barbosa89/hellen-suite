<template>
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-3">
            <icon-card
                :content='cards.rooms.content'
                :icon='cards.rooms.icon'
                :bg='cards.rooms.bg'
                :url='cards.rooms.url'>
            </icon-card>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <icon-card
                :content='cards.guests.content'
                :icon='cards.guests.icon'
                :bg='cards.guests.bg'
                :url='cards.guests.url'>
            </icon-card>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <icon-card
                :content='cards.companies.content'
                :icon='cards.companies.icon'
                :bg='cards.companies.bg'
                :url='cards.companies.url'>
            </icon-card>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <icon-card
                :content='cards.vouchers.content'
                :icon='cards.vouchers.icon'
                :bg='cards.vouchers.bg'
                :url='cards.vouchers.url'>
            </icon-card>
        </div>
    </div>
</template>

<script>
    import IconCard from './IconCard'

    export default {
        mounted() {
            this.buildRoomData()
        },
        props: {
            hotelId: {
                type: String,
                default: function () {
                    return ''
                }
            }
        },
        data() {
            return {
                cards: {
                    rooms: {
                        content: '',
                        icon: 'fa-bed',
                        bg: 'bg-info',
                        url: route('rooms.index')
                    },
                    guests: {
                        content: '',
                        icon: 'fa-users',
                        bg: 'bg-warning',
                        url: route('guests.index')
                    },
                    companies: {
                        content: '',
                        icon: 'fa-building',
                        bg: 'bg-success',
                        url: route('companies.index')
                    },
                    vouchers: {
                        content: '',
                        icon: 'fa-receipt',
                        bg: 'bg-dark',
                        url: route('vouchers.index')
                    }
                }
            }
        },
        components: {
            IconCard
        },
        watch: {
            hotelId() {
                this.buildRoomData()
            }
        },
        methods: {
            buildRoomData() {
                axios
                    .get(route('api.web.rooms.index', this.hotelId))
                    .then(response => {
                        if (response.data.rooms) {
                            let rooms = response.data.rooms
                            let assigned = _.filter(rooms, {status: '1'})

                            this.cards.rooms.content = `${assigned.length} / ${rooms.length} ${this.$root.$t('rooms.title')}`
                        }
                    })
            }
        },
    }
</script>
