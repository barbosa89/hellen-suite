<template>
    <div class="modal fade" id="voucher-filter" tabindex="-1" aria-labelledby="voucherFilterLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voucherFilterLabel">{{ $t('vouchers.filters') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>{{ $t('common.types') }}</h4>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-icon p-smooth">
                                    <input type="checkbox" name="type[]" v-model="type" value="lodging"/>
                                    <div class="state p-primary">
                                        <em class="icon fa fa-check"></em>
                                        <label>{{ $t('transactions.lodging') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-icon p-smooth">
                                    <input type="checkbox" name="type[]" v-model="type" value="sale"/>
                                    <div class="state p-primary">
                                        <em class="icon fa fa-check"></em>
                                        <label>{{ $t('transactions.sale') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-icon p-smooth">
                                    <input type="checkbox" name="type[]" v-model="type" value="entry"/>
                                    <div class="state p-primary">
                                        <em class="icon fa fa-check"></em>
                                        <label>{{ $t('transactions.entry') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-icon p-smooth">
                                    <input type="checkbox" name="type[]" v-model="type" value="loss"/>
                                    <div class="state p-primary">
                                        <em class="icon fa fa-check"></em>
                                        <label>{{ $t('transactions.loss') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-icon p-smooth">
                                    <input type="checkbox" name="type[]" v-model="type" value="discard"/>
                                    <div class="state p-primary">
                                        <em class="icon fa fa-check"></em>
                                        <label>{{ $t('transactions.discard') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-icon p-smooth">
                                    <input type="checkbox" name="type[]" v-model="type" value="dining"/>
                                    <div class="state p-primary">
                                        <em class="icon fa fa-check"></em>
                                        <label>{{ $t('transactions.dining') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>{{ $t("common.status") }}</h4>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="open" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('vouchers.open') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="closed" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('vouchers.closed') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="paid" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('invoices.status.paid') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="pending" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('invoices.status.pending') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 my-1">
                                <div class="pretty p-default p-curve">
                                    <input type="radio" name="status" v-model="status" value="reservation" />
                                    <div class="state p-primary-o">
                                        <label>{{ $t('vouchers.reservation') }}</label>
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
                type: [],
                status: ''
            }
        },
        methods: {
            clear() {
                this.type = []
                this.status = ''

                this.$emit('filter', {})
            },
            apply() {
                this.$emit('filter', {
                    type: this.type,
                    status: [this.status]
                })
            }
        },
    }
</script>
