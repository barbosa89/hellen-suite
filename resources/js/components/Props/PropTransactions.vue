<script>
    import Transactions from '../Transactions/TransactionsBase';

    export default {
        data() {
            return {
                process_uri: '/props/vouchers',
                search_uri: '/props/search',
                module_uri: '/props',
                title: this.$root.$t('props.transactions'),
                module_name: this.$root.$t('props.title'),
                types: [
                    {
                        description: this.$root.$t('transactions.entry'),
                        value: 'entry',
                        permission: 'transactions.entry'
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