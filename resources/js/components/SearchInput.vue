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
import { toast } from 'vue3-toastify'
import { trans } from 'laravel-vue-i18n'

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
                                toast.info(trans('common.without.results'))
                            }
                        } else {
                            this.$emit('results', response.data)
                        }

                    }).catch(e => {
                        toast.error(trans('common.try'))
                    })
            }
        }
    }
},
}
</script>
