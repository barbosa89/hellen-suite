<script>
    import Transactions from '../Transactions/TransactionsBase';

    export default {
        data() {
            return {
                process_uri: '/products/process',
                search_uri: '/products/search',
                module_uri: '/products',
                title: this.$root.$t('products.transactions'),
                module_name: this.$root.$t('products.title')
            }
        },
        extends: Transactions
    }
</script>