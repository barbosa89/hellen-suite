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
            url: {
                type: String,
                default: function () {
                    return ''
                }
            },
            hotel: {
                type: String,
                default: function () {
                    return ''
                }
            },
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
                if (current.length >= 3) {
                    let params = {
                        query_by: this.query,
                    }

                    if (this.hotel.length > 0) {
                        params.hotel = this.hotel
                    }

                    axios
                        .get(this.url, {
                            params: params
                        })
                        .then(response => {
                            if (response.data.hasOwnProperty('results')) {
                                let results = response.data.results

                                if (results.length > 0) {
                                    this.$emit('results', results)
                                } else {
                                    toastr.info(
                                        this.$root.$t('common.without.results'),
                                        this.$root.$t('common.sorry')
                                    )
                                }
                            } else {
                                this.$emit('results', response.data)
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
    },
    }
</script>
