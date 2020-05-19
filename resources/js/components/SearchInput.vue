<template>
    <div class="input-group">
        <input class="form-control" type="search" v-model="query" :placeholder='$t("common.search")'>
        <div class="input-group-append">
            <button class="input-group-text" id="btnGroupAddon">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            url: '',
            hotel: ''
        },
        data() {
            return {
                query: ''
            }
        },
    watch: {
        query: function(current, old) {
            if (current.length == 0 || this.query.length == 0) {
                this.$emit('reset')
            } else {
                if (this.hotel.length == 0) {
                    toastr.info(
                        this.$root.$t('hotels.choose'),
                        'Ey'
                    )
                } else {
                    if (current.length >= 3 && this.hotel.length > 0) {
                        axios.get(this.url + `?query=${this.query}&hotel=${this.hotel}`)
                            .then(response => {
                                let results = response.data.results

                                if (results.length > 0) {
                                    this.$emit('results', results)
                                } else {
                                    toastr.info(
                                        this.$root.$t('common.without.results'),
                                        this.$root.$t('common.sorry')
                                    )
                                }
                            }).catch(e => {
                                toastr.error(
                                    this.$root.$t('common.try'),
                                    'Error'
                                )
                            })
                    }
                }
            }
        }
    },
    }
</script>