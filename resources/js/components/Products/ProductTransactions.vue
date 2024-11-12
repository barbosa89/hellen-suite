<script>
import { wTrans } from "laravel-vue-i18n"
import Transactions from '../Transactions/TransactionsBase.vue'

export default {
    data() {
        return {
            process_uri: '/products/vouchers',
            search_uri: '/products/search',
            module_uri: '/products',
            title: wTrans('products.transactions'),
            module_name: wTrans('products.title'),
            types: [
                {
                    description: wTrans('transactions.entry'),
                    value: 'entry',
                    permission: 'transactions.entry'
                },
                {
                    description: wTrans('transactions.sale'),
                    value: 'sale',
                    permission: 'transactions.sale'
                },
                {
                    description: wTrans('transactions.discard'),
                    value: 'discard',
                    permission: 'transactions.discard'
                },
                {
                    description: wTrans('transactions.loss'),
                    value: 'loss',
                    permission: 'transactions.loss'
                }
            ]
        }
    },
    extends: Transactions
}
</script>