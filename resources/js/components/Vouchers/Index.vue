<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light app-nav border border-top-0 border-right-0 border-left-0">
            <a href="/vouchers" class="navbar-brand text-muted">
                {{ $t('vouchers.title') }}
            </a>
            <button type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbarNavDropdown" class="collapse navbar-collapse">
                <form class="form-inline my-2 my-lg-0">
                    <hotel-select @hotel="hotelHash = $event"></hotel-select>
                </form>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a :href='route("vouchers.process")' rel="noopener noreferrer" class="nav-link">
                            {{ $t('vouchers.process') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" data-toggle="modal" data-target="#voucher-filter" class="nav-link">
                            {{ $t('common.filters.filters') }}
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
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
    import VueTable from '@barbosa89/vue-table'
    import FilterModal from './FilterModal'

    export default {
        mounted() {
            this.lang = document.documentElement.lang
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
