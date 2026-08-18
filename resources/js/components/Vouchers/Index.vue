<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light app-nav">
            <a href="/vouchers" class="navbar-brand text-body-secondary">
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
                        <td>{{ record.created_at }}</td>
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
import { wTrans } from "laravel-vue-i18n"

import FilterModal from './FilterModal.vue'

export default {
    mounted() {
        this.lang = document.documentElement.lang
    },
    components: {
        FilterModal
    },
    computed: {
        canDisplayList() {
            return this.url.length > 0
        },
        headers() {
            return [
                {
                    description: wwTrans('common.date')
                },
                {
                    description: wwTrans('common.number'),
                    sortable: 'number'
                },
                {
                    description: wwTrans('common.type'),
                    sortable: 'type'
                },
                {
                    description: wwTrans('common.value')
                },
            ]
        }
    },
    data() {
        return {
            url: '',
            hotelHash: '',
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
            const modal = new bootstrap.Modal('#voucher-filter')
            modal.show()
        },
        toggle() {
            const modal = new bootstrap.Modal('#voucher-filter')
            modal.toggle()
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
