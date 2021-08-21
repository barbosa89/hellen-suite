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
    import Bus from '../../Bus'

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
                    .finally(() => {
                        if (this.cards.guests.content.length == 0) {
                            this.builGuestData()
                        }

                        this.builVoucherData()
                    })
            },
            builGuestData() {
                axios
                    .get(route('api.v1.guests.index'))
                    .then(response => {
                        const quantity = response.data.data.length

                        if (quantity == 0) {
                            this.cards.guests.content = this.$root.$t('guests.new.none')
                        } else if (quantity == 1) {
                            this.cards.guests.content = `${quantity} ${this.$root.$t('guests.new.one')}`
                        } else {
                            this.cards.guests.content = `${quantity} ${this.$root.$t('guests.new.many')}`
                        }
                    })
                    .finally(() => {
                        if (this.cards.companies.content.length == 0) {
                            this.builCompanyData()
                        }
                    })
            },
            builCompanyData() {
                axios
                    .get(route('api.web.companies.index'))
                    .then(response => {
                        const quantity = response.data.companies.data.length

                        if (quantity == 0) {
                            this.cards.companies.content = this.$root.$t('companies.new.none')
                        } else if (quantity == 1) {
                            this.cards.companies.content = `${quantity} ${this.$root.$t('companies.new.one')}`
                        } else {
                            this.cards.companies.content = `${quantity} ${this.$root.$t('companies.new.many')}`
                        }
                    })
            },
            builVoucherData() {
                const date = moment().subtract(1, 'days').format('Y-M-D')

                axios
                    .get(route('api.web.vouchers.index', this.hotelId), {
                        params: {
                            from_date: date
                        }
                    })
                    .then(response => {
                        const quantity = response.data.vouchers.data.length

                        if (quantity > 0) {
                            if (quantity == 1) {
                                this.cards.vouchers.content = `${quantity} ${this.$root.$t('vouchers.new.one')}`
                            } else {
                                this.cards.vouchers.content = `${quantity} ${this.$root.$t('vouchers.new.many')}`
                            }

                            Bus.$emit('last-vouchers', response.data.vouchers.data)
                        } else {
                            this.cards.vouchers.content = this.$root.$t('vouchers.new.none')
                        }
                    })
            }
        },
    }
</script>
