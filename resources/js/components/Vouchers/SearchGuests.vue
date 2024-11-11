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
                            <button class="btn btn-dark btn-sm" @click="redirect(record)">
                                <em class="fa fa-plus"></em>
                            </button>
                        </td>
                </template>
            </vue-table>
        </template>
    </div>
</template>

<script>
import { toast } from 'vue3-toastify'
import { wTrans } from 'laravel-vue-i18n'

export default {
    props: {
        voucherHash: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            url: route('api.web.guests.index', {status: 'is_not_staying', per_page: 100}),
            guests: [],
            headers: [
                {
                    description: wTrans('common.name')
                },
                {
                    description: wTrans('common.idNumber')
                },
                {
                    description: wTrans('common.options')
                },
            ]
        }
    },
    methods: {
        setData(data) {
            if (data.guests.data.length > 0) {
                this.guests = []
                setTimeout(() => {
                    this.guests = data.guests.data
                }, 500)
            } else {
                toast.info(wTrans('common.without.results'))
            }
        },
        redirect(guest) {
            let route = window.route('vouchers.guests', {id: this.voucherHash, guest: guest.hash})

            window.location.href = route
        }
    }
}
</script>
