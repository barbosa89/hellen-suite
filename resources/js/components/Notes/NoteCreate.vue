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
                <hotel-select @hotel="hotel = $event"></hotel-select>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-12">
                <editor
                v-model="content"
                api-key="no-api-key"
                :init="{
                    height: 500,
                    menubar: false,
                    plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar:
                    'undo redo | formatselect | bold italic backcolor | \
                    alignleft aligncenter alignright alignjustify | \
                    bullist numlist outdent indent | removeformat | help'
                }"
                />
            </div>
        </div>

        <div class="row my-2">
            <div class="col-12">
                <label>Tags:</label>
                <tags-input element-id="tags"
                v-model="selected_tags"
                :existing-tags="tags"
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
    import VoerroTagsInput from '@voerro/vue-tagsinput'
    import Editor from '@tinymce/tinymce-vue'

    export default {
        mounted() {
            this.loadTags()
        },
        data() {
            return {
                hotels: [],
                selected_tags: [],
                hotel: '',
                content: '',
                add: false,
                tags: [],
                errors: []
            }
        },
        components: {
            "tags-input": VoerroTagsInput,
            'editor': Editor
        },
        watch: {
            content(current, old) {
                // Check hashtags in content
                this.checkVoucherNumbers()
            }
        },
        methods: {
            loadTags() {
                axios.get('/tags')
                    .then(response => {
                        if (response.data.length) {
                            this.tags = response.data
                        }
                    })
            },
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
                        if (result.value) {
                            this.send()
                        }
                    })
                } else {
                    if (this.errors.length == 0) {
                        toastr.info(
                            this.$root.$t('notes.check'),
                            this.$root.$t('common.sorry')
                        )
                    }

                    this.showErrors()
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

                if (this.errors.length) {
                    return false
                }

                return true
            },
            send() {
                axios.post('/notes', {
                    hotel_id: this.hotel,
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
                        this.tags.push({
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
                let results = _.find(this.tags, tag => {
                    return tag.hash == hash
                })

                return typeof results === Object
            },
            reset() {
                this.selected_tags = []
                this.hotel = ''
                this.content = ''
                this.add = false
                this.errors = []
            },
            checkVoucherNumbers() {
                // Reset errors
                this.errors = []

                // Get all hashtags
                let hashtags =  this.getHashtags()

                // Validate each voucher number exists
                this.checkHashtags(hashtags)
            },
            getHashtags() {
                return this.content.match(/#(\w+)/g);
            },
            checkHashtags(hashtags) {
                _.each(hashtags, hashtag => {
                    let number = hashtag.replace('#', '')

                    if (number.length == 12) {
                        axios.get('/vouchers/search?query=' + number)
                        .then(response => {
                            if (response.data.data.length == 0) {
                                this.errors.push(this.$root.$t('vouchers.notfound') + ': #' + number)
                            }
                        })
                    }
                })
            },
            showErrors() {
                _.each(this.errors, error => {
                    toastr.error(
                        error,
                        'Error'
                    )
                })
            }
        },
    }
</script>