<template>
    <div>
        <div class="row mt-3" v-if="canDisplayList">
            <div class="col">
                <vue-table
                    :url='url'
                    :headers='headers'
                    :data-key='"vouchers"'
                    :lang='lang'
                    :search-icon='"fas fa-search"'
                    :params='params'>
                    <template v-slot:record="{ record }">
                        <td>{{ record.created_at | date }}</td>
                        <td>
                            <a :href='route("vouchers.show", record.hash)'>
                                {{ record.number }}
                            </a>
                        </td>
                        <td>{{ $t('transactions.' + record.type) }}</td>
                        <td>{{ record.value }}</td>
                    </template>
                </vue-table>
            </div>
        </div>

        <filter-modal @filter='setFilters'></filter-modal>
    </div>
</template>

<script>
import FilterModal from './FilterModal'
import VueTable from '@barbosa89/vue-table'

export default {
    mounted() {
        this.lang = document.documentElement.lang

        this.$root.$on('hotel-selected', (hash) => {
            this.hotelHash = hash
        })
    },
    components: {
        VueTable,
        FilterModal
    },
    computed: {
        canDisplayList() {
            return this.url.length > 0
        }
    },
    data() {
        return {
            url: '',
            hotelHash: '',
            headers: [
                {
                    description: this.$root.$t('common.date')
                },
                {
                    description: this.$root.$t('common.number'),
                    sortable: 'number'
                },
                {
                    description: this.$root.$t('common.type'),
                    sortable: 'type'
                },
                {
                    description: this.$root.$t('common.value')
                },
            ],
            lang: '',
            params: {}
        }
    },
    watch: {
        hotelHash() {
            if (this.hotelHash.length) {
                this.url = ''

                this.url = route('api.web.vouchers.index', this.hotelHash)
            }
        }
    },
    methods: {
        close() {
            $('#voucher-filter').modal('hide')
        },
        toggle() {
            $('#voucher-filter').modal('toggle')
        },
        setFilters(filters) {
            const params = {}

            if (filters.hasOwnProperty('status') && filters.status.length) {
                params.status = filters.status
            }

            if (filters.hasOwnProperty('type') && filters.type.length) {
                params.type = filters.type
            }

            this.params = Object.assign({}, params)

            this.close()
        }
    }
}
</script>
