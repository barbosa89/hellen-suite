<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="text-primary-600 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 rounded border-neutral-300 shadow-sm dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:ring-offset-neutral-950"
    />
</template>
