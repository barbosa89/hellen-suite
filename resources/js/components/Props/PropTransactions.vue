<script>
import { trans } from "laravel-vue-i18n"
import Transactions from '../Transactions/TransactionsBase.vue';

export default {
    data() {
        return {
            process_uri: '/props/vouchers',
            search_uri: '/props/search',
            module_uri: '/props',
            title: trans('props.transactions'),
            module_name: trans('props.title'),
            types: [
                {
                    description: trans('transactions.entry'),
                    value: 'entry',
                    permission: 'transactions.entry'
                },
                {
                    description: trans('transactions.discard'),
                    value: 'discard',
                    permission: 'transactions.discard'
                },
                {
                    description: trans('transactions.loss'),
                    value: 'loss',
                    permission: 'transactions.loss'
                }
            ]
        }
    },
    extends: Transactions
}
</script>