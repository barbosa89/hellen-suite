<script>
import { trans } from "laravel-vue-i18n"
import Transactions from '../Transactions/TransactionsBase.vue';

export default {
    data() {
        return {
            process_uri: '/props/vouchers',
            search_uri: '/props/search',
            module_uri: '/props',
            title: wTrans('props.transactions'),
            module_name: wTrans('props.title'),
            types: [
                {
                    description: wTrans('transactions.entry'),
                    value: 'entry',
                    permission: 'transactions.entry'
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