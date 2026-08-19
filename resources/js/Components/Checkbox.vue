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
        class="text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-sm border-neutral-300 shadow-xs dark:border-neutral-700 dark:bg-neutral-900 dark:focus:ring-offset-neutral-950"
    />
</template>
