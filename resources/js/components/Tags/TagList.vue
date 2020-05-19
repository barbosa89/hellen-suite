<template>
    <div>
        <div class="row my-2">
            <div class="col-12">
                <label for="hotel">Hotel:</label>
                <hotel-select @hotel="hotel = $event"></hotel-select>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <a href="#" v-for="(tag, index) in tags" :key="index" @click.prevent="go(tag)">
                    <h3 class="btn btn-secondary m-1">
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
                hotel: {
                    type: String,
                    default: ''
                }
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
            }
        },
    }
</script>

<style scoped>
    h3 {
        font-size: 1em;
    }
</style>