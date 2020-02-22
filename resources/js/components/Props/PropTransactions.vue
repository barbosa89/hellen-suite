<script>
    import Transactions from '../Transactions/TransactionsBase';

    export default {
        data() {
            return {
                process_uri: '/props/vouchers',
                search_uri: '/props/search',
                module_uri: '/props',
                title: this.$root.$t('props.transactions'),
                module_name: this.$root.$t('props.title')
            }
        },
        extends: Transactions
    }
</script>