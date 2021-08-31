<template>
    <div class="modal fade" id="guest-filters" tabindex="-1" aria-labelledby="guestFilterLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guestFilterLabel">{{ $t('guests.filters') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>{{ $t("common.status") }}</h4>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="is_staying" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('guests.status.hosted') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="is_not_staying" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('guests.status.out') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">{{ $t('common.close') }}</button>
                    <button class="btn btn-secondary" @click.prevent="clear">{{ $t('common.clear') }}</button>
                    <button class="btn btn-primary" @click="apply">{{ $t('common.filters.apply') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                status: ''
            }
        },
        methods: {
            clear() {
                this.status = ''

                this.$emit('filter', {})
            },
            apply() {
                this.$emit('filter', {
                    status: this.status
                })
            }
        },
    }
</script>
