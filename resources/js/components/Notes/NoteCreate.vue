<template>
    <div>
        <div class="row my-2">
            <div class="col-12">
                <h2 class="text-center">{{ $t('common.creationOf') }} {{ $t('notes.title') }}</h2>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-12">
                <label for="hotel">Hotel:</label>
                <select name="hotel" id="hotel" class="form-control" v-model="hotel">
                    <option :value="''" disabled selected>{{ $t('common.chooseOption') }}</option>
                    <option v-for="(hotel, index) in hotels" :key="index" :value="hotel.hash">
                        {{ hotel.business_name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-12">
                <label for="content">Content:</label>
                <textarea name="content" id="content" cols="30" rows="5" class="form-control" v-model="content"></textarea>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-12">
                <label>Tags:</label>
                <tags-input element-id="tags"
                v-model="selected_tags"
                :existing-tags="tag_list"
                :typeahead="true"
                :add-tags-on-space="true"
                :add-tags-on-comma="true"
                @tag-added="tagAdded"
                :typeahead-style="'dropdown'">
                </tags-input>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-12">
                <div class="pretty p-icon p-smooth d-block my-2">
                    <input type="checkbox" name="add" v-model="add">
                    <div class="state p-primary">
                        <i class="icon fa fa-check"></i>
                        <label>{{ $t('notes.add') }}</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-12">
                <button role="button" class="btn btn-primary" @click="create">
                    {{ $t('common.create') }}
                </button>

                <a href="/notes" class="btn btn-secondary">
                    {{ $t('common.back') }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import VoerroTagsInput from '@voerro/vue-tagsinput';

    export default {
        props: {
            hotels: Array,
            tags: Array
        },
        data() {
            return {
                selected_tags: [],
                hotel: '',
                content: '',
                add: false,
                tag_list: this.tags
            }
        },
        components: {
            "tags-input": VoerroTagsInput
        },
        computed: {
            lang() {
                let lang = document.documentElement.lang

                if (lang = 'es') {
                    return this.es
                }


                if (lang = 'en') {
                    return this.en
                }
            }
        },
        methods: {
            tagAdded(tag) {
                if (tag.hasOwnProperty('key')) {
                    this.createTag(tag)
                }
            },
            create() {
                if (this.validate()) {
                    Swal.fire({
                        title: this.$root.$t('common.confirm'),
                        text: this.$root.$t('common.confirmAction'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: this.$root.$t('common.continue'),
                        cancelButtonText: this.$root.$t('common.cancel')
                    }).then((result) => {
                        this.send()
                    })
                } else {
                    toastr.info(
                        this.$root.$t('common.error'),
                        this.$root.$t('common.sorry')
                    )
                }
            },
            validate() {
                if (this.hotel.length == 0) {
                    return false
                }

                if (this.content.length == 0) {
                    return false
                }

                if (this.selected_tags.length == 0) {
                    return false
                }

                return true
            },
            send() {
                axios.post('/notes', {
                    hotel: this.hotel,
                    content: this.content,
                    tags: this.selected_tags,
                    add: this.add
                }).then(response => {
                    if (response.data.status) {
                        this.reset()

                        toastr.success(
                            this.$root.$t('common.createdSuccessfully'),
                            this.$root.$t('common.great'),
                        )
                    }
                }).catch(error => {
                    toastr.error(
                        this.$root.$t('common.error'),
                        'Error'
                    )
                })
            },
            createTag(tag) {
                axios.post('/tags', {
                    tag: tag.value
                }).then(response => {
                    this.$set(tag, 'hash', response.data.hash)

                    if (!this.existsTag(response.data.hash)) {
                        this.tag_list.push({
                            hash: response.data.hash,
                            value: response.data.value,
                        })
                    }

                }).catch(error => {
                    toastr.error(
                        this.$root.$t('common.error'),
                        'Error'
                    )
                })
            },
            existsTag(hash) {
                let results = _.find(this.tag_list, tag => {
                    return tag.hash == hash
                })

                return typeof results === Object
            },
            reset() {
                this.selected_tags = []
                this.hotel = ''
                this.content = ''
                this.add = false
            }
        },
    }
</script>