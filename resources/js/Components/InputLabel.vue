<script setup>
defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label
        class="flex items-center gap-1.5 text-sm font-semibold text-neutral-800 dark:text-neutral-200"
    >
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span
            v-if="required"
            class="text-danger-600 dark:text-danger-400"
            aria-hidden="true"
            >*</span
        >
    </label>
</template>
