<template>
    <div class="input-group">
        <input class="form-control" type="search" v-model="search" :placeholder='$t("common.search")'>
        <div class="input-group-append">
            <button class="input-group-text" id="btnGroupAddon">
                <em class="fa fa-search"></em>
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
                search: ''
            }
        },
    watch: {
        search(current, old) {
            if (current.length == 0 || this.search.length == 0) {
                this.$emit('reset')
            } else {
                if (current.length >= 3) {
                    let params = {
                        search: this.search,
                    }

                    if (this.hotel.length > 0) {
                        params.hotel = this.hotel
                    }

                    axios
                        .get(this.url, {
                            params: params
                        })
                        .then(response => {
                            this.$emit('response', response.data)
                        }).catch(() => {
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
