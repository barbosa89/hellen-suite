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
                <select name="hotel" id="hotel" class="form-control">
                    <option v-for="(hotel, index) in hotels" :key="index" :value="hotel.hash">
                        {{ hotel.business_name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-12">
                <label for="content">Content:</label>
                <textarea name="content" id="content" cols="30" rows="5" class="form-control"></textarea>
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
                @tag-added="addedTag"
                :typeahead-style="'dropdown'">
                </tags-input>
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
                date: ''
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
            addedTag(e) {

            }
        },
    }
</script>