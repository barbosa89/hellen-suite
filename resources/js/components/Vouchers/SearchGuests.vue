<template>
    <div>
        <search-input :url='url' @response='setData'></search-input>

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

        <guest-creation-modal
            :genders="genders"
            :voucher-hash="voucherHash">
        </guest-creation-modal>
    </div>
</template>

<script>
import SearchInput from '../SearchInput'
import VueTable from '@barbosa89/vue-table'
import GuestCreationModal from '../Guests/CreateModal'

export default {
    props: {
        voucherHash: {
            type: String,
            required: true
        },
        genders: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            url: route('api.v1.guests.index', {status: 'is_not_staying'}),
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
        VueTable,
        SearchInput,
        GuestCreationModal
    },
    methods: {
        setData(data) {
            if (data.data.length > 0) {
                this.guests = data.data
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
