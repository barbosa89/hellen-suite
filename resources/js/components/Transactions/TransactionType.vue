<template>
    <select name="type" id="type" v-model="type" @change="push" class="form-control">
        <option :value="null" disabled selected>{{ $t('transactions.select.type') }}</option>
        <option value="discharge">{{ $t('transactions.discharge') }}</option>
        <option value="entry">{{ $t('transactions.entry') }}</option>
        <option value="sales">{{ $t('transactions.sales') }}</option>
        <option value="losses">{{ $t('transactions.losses') }}</option>
    </select>
</template>

<script>
    export default {
        data() {
            return {
                type: ''
            }
        },
        methods: {
            push() {
                this.$emit('selectType', this.type)
            }
        },
    }
</script>