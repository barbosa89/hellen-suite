<script>
import { trans } from "laravel-vue-i18n"
import Transactions from '../Transactions/TransactionsBase.vue'

export default {
    data() {
        return {
            process_uri: '/products/vouchers',
            search_uri: '/products/search',
            module_uri: '/products',
            title: trans('products.transactions'),
            module_name: trans('products.title'),
            types: [
                {
                    description: trans('transactions.entry'),
                    value: 'entry',
                    permission: 'transactions.entry'
                },
                {
                    description: trans('transactions.sale'),
                    value: 'sale',
                    permission: 'transactions.sale'
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