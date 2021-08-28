export default {
    methods: {
        status(status) {
            switch (status) {
                case 0:
                    return this.$root.$t('guests.status.out')
                case 1:
                    return this.$root.$t('guests.status.hosted')
                default:
                    return this.$root.$t('guests.status.out')
            }
        }
    }
}