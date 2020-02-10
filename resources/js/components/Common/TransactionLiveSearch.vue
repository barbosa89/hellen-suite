<template>
    <div>
        <form class="form-inline my-2 my-lg-0" method="get">
            <div class="ui search focus">
                <div class="ui left icon input">
                    <div class="input-group">
                        <input class="form-control" type="search" name="query" v-model="query" :placeholder='$t("common.search")' aria-label="Search" required>
                    </div>
                </div>
                <transition name="fade">
                    <div class="results transition visible" v-if="results.length != 0" style="display: block !important;">
                        <a class="result" v-for="result in results" :key="result.hash" @click.prevent="push(result)">
                            <div class="content">
                                <div class="title">{{ result.description }}</div>
                                <div class="description">
                                    {{ $t('common.current.stock') }}: {{ result.quantity || $t('services.quantity') }}
                                </div>
                            </div>
                        </a>
                    </div>
                </transition>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                query: '',
                results: []
            }
        },
        props: ['uri', 'hotel'],
        methods: {
            push(result) {
                this.$emit('selectResult', result)
                this.query = ''
            }
        },
        watch: {
            query: function(current, old) {
                if (current.length == 0 || this.query.length == 0) {
                    this.results = []
                } else {
                    if (current.length >= 3 && this.hotel.length > 0) {
                        axios.post(this.uri, {
                            query: this.query,
                            hotel: this.hotel
                        }).then(response => {
                            let results = JSON.parse(response.data.results);

                            if (results.length > 0) {
                                this.results = results
                            } else {
                                this.results = []

                                toastr.info(
                                    this.$root.$t('common.without.results'),
                                    this.$root.$t('common.sorry')
                                );
                            }
                        }).catch(e => {
                            toastr.error(
                                this.$root.$t('common.try'),
                                'Error'
                            );
                        });
                    }
                }
            }
        },
    }
</script>