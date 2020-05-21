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
                <a href="#" v-for="(tag, index) in list" :key="index" @click.prevent="go(tag)" @contextmenu.prevent="$refs.menu.open($event, { tag })">
                    <h3 class="btn btn-outline-info m-1">
                        {{ tag.description }}
                    </h3>
                </a>
            </div>
        </div>


        <vue-context ref="menu">
            <template slot-scope="child">
                <li>
                    <a href="#" @click.prevent="edit($event.target.innerText, child.data)">
                        {{ $t('common.edit') }}
                    </a>
                </li>
                <li>
                    <a href="#" @click.prevent="destroy($event.target.innerText, child.data)">
                        {{ $t('common.delete.item') }}
                    </a>
                </li>
            </template>
        </vue-context>
    </div>
</template>

<script>
    import { VueContext } from 'vue-context';

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
        components: {
            VueContext
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
            },
            edit(text, data) {
                window.location.href = `/tags/${data.tag.hash}/edit`
            },
            destroy(text, data) {
                axios.delete(`tags/${data.tag.hash}`)
                    .then(response => {
                        if (response.data.status) {
                            this.list = _.filter(this.list, tag => {
                                return tag.hash != data.tag.hash
                            })

                            toastr.success(
                                this.$root.$t('common.deletedSuccessfully'),
                                this.$root.$t('common.great'),
                            )
                        }
                    }).catch(error => {
                        toastr.info(
                            this.$root.$t('common.error'),
                            'Error',
                        )
                    })
            }
        },
    }
</script>

<style scoped>
    h3 {
        font-size: 1em;
    }
</style>