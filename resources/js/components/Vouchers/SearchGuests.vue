<template>
    <div>
        <search-input :url='url' @results='setData'></search-input>

        <template v-if="guests.length > 0">
            <vue-table :headers='headers' :user-data='guests'>
                <template v-slot:record="{ record }">
                        <td>
                            <a href="#" @click.prevent="redirect(record)">
                                {{ record.full_name }}
                            </a>
                        </td>
                        <td>
                            <a href="#" @click.prevent="redirect(record)">
                                {{ record.dni }}
                            </a>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" @click="redirect(record)">
                                <em class="fa fa-plus"></em>
                            </button>
                        </td>
                </template>
            </vue-table>
        </template>
    </div>
</template>

<script>
    import SearchInput from '../SearchInput'
    import VueTable from '@barbosa89/vue-table'

    export default {
        props: {
            voucherHash: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                url: route('api.web.guests.index', {status: 'is_not_staying'}),
                guests: [],
                headers: [
                    {
                        description: this.$root.$t('common.name')
                    },
                    {
                        description: this.$root.$t('common.idNumber')
                    },
                    {
                        description: this.$root.$t('common.options')
                    },
                ]
            }
        },
        components: {
            SearchInput,
            VueTable
        },
        methods: {
            setData(data) {
                if (data.guests.data.length > 0) {
                    this.guests = data.guests.data
                } else {
                    toastr.info(
                        this.$root.$t('common.without.results'),
                        this.$root.$t('common.sorry')
                    )
                }
            },
            redirect(guest) {
                let route = window.route('vouchers.guests', {id: this.voucherHash, guest: guest.hash})

                window.location.href = route
            }
        }
    }
</script>
