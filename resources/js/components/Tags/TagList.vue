<template>
    <div>
        <div class="row mt-2 mb-4">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <hotel-select @hotel="hotel = $event"></hotel-select>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <search-input @results="showResults" @reset="reset" :hotel="'not-required'" :url="'/tags/search'"></search-input>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <a href="#" v-for="(tag, index) in list" :key="index" @click.prevent="go(tag)">
                    <h3 class="btn btn-outline-info m-1">
                        {{ tag.slug }}
                    </h3>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            tags: Array
        },
        data() {
            return {
                hotel: '',
                list: this.tags
            }
        },
        methods: {
            go(tag) {
                // Redirect on click
                if (this.hotel.length > 0) {
                    window.location.href = '/tags/' + tag.hash + '/hotel/' + this.hotel
                } else {
                    toastr.info(
                        this.$root.$t('hotels.choose'),
                        'Ey'
                    )
                }
            },
            showResults(results) {
                this.list = results
            },
            reset() {
                this.list = this.tags
            }
        },
    }
</script>

<style scoped>
    h3 {
        font-size: 1em;
    }
</style>