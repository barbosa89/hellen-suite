<template>
    <span v-if="hasMessage" class="invalid-feedback d-block" role="alert">
        <strong>{{ message }}</strong>
    </span>
</template>
<script>
export default {
    props: {
        message: {
            type: String,
            default: function() {
                return ''
            }
        }
    },
    computed: {
        hasMessage() {
            return this.message.length > 0
        }
    }
}
</script>
