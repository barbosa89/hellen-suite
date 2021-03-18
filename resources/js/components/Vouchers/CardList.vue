<template>
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-receipt"></i>
            {{ $t('vouchers.title') }}
        </div>
        <div class="card-body p-0">
            <template v-if="vouchers.length > 0">
                <vue-table :headers='headers' :user-data='vouchers'>
                    <template v-slot:record="{ record }">
                        <td>{{ record.created_at | date }}</td>
                        <td>
                            <a :href="'/vouchers/' +record.hash">
                                {{ record.number }}
                            </a>
                        </td>
                        <td>{{ record.hotel.business_name }}</td>
                        <td>{{ record.reservation ? $t('vouchers.reservation') : $t('vouchers.checkin') }}</td>
                        <td>{{ new Intl.NumberFormat("de-DE").format(record.value) }}</td>
                    </template>
                </vue-table>
            </template>
            <template v-else>
                <div v-if="empty == false" class="p-4 d-flex justify-content-center">
                    <em class="fas fa-spinner fa-pulse"></em>
                </div>
                <div v-else class="p-4">
                    <p class="m-0">{{ $t('common.noRecords') }}</p>
                </div>
            </template>
        </div>
        <div class="card-footer small text-muted">{{ $t('common.updated.at') }}: {{ date.format('YY-MM-DD HH:mm:ss') }}</div>
    </div>
</template>

<script>
    import Bus from '../../Bus'
    import VueTable from '@barbosa89/vue-table'

    export default {
        mounted() {
            Bus.$on('last-vouchers', (vouchers) => {
                this.vouchers = vouchers
            })

            setTimeout(() => {
                this.empty = true
            }, 5000);
        },
        data() {
            return {
                vouchers: [],
                date: moment(),
                empty: false,
                headers: [
                    {
                        description: this.$root.$t('common.date')
                    },
                    {
                        description: this.$root.$t('common.number')
                    },
                    {
                        description: 'Hotel'
                    },
                    {
                        description: this.$root.$t('common.type')
                    },
                    {
                        description: this.$root.$t('common.value')
                    },
                ]
            }
        },
        components: {
            VueTable
        },
    }
</script>
