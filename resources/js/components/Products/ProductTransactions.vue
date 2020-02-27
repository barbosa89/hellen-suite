<script>
    import Transactions from '../Transactions/TransactionsBase';

    export default {
        data() {
            return {
                process_uri: '/products/vouchers',
                search_uri: '/products/search',
                module_uri: '/products',
                title: this.$root.$t('products.transactions'),
                module_name: this.$root.$t('products.title'),
                types: [
                    {
                        description: this.$root.$t('transactions.entry'),
                        value: 'entry',
                        permission: 'transactions.entry'
                    },
                    {
                        description: this.$root.$t('transactions.sale'),
                        value: 'sale',
                        permission: 'transactions.sale'
                    },
                    {
                        description: this.$root.$t('transactions.discard'),
                        value: 'discard',
                        permission: 'transactions.discard'
                    },
                    {
                        description: this.$root.$t('transactions.loss'),
                        value: 'loss',
                        permission: 'transactions.loss'
                    }
                ]
            }
        },
        extends: Transactions
    }
</script>