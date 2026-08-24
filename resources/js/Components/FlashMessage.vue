<script setup>
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    message: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error'].includes(value),
    },
});
</script>

<template>
    <div
        class="flex items-start gap-3 rounded-xl px-4 py-3 text-sm font-medium ring-1"
        :class="
            variant === 'success'
                ? 'bg-success-50 text-success-800 ring-success-200 dark:bg-success-900/25 dark:text-success-300 dark:ring-success-800'
                : 'bg-danger-50 text-danger-800 ring-danger-200 dark:bg-danger-900/25 dark:text-danger-300 dark:ring-danger-800'
        "
        :role="variant === 'success' ? 'status' : 'alert'"
    >
        <CheckCircleIcon
            v-if="variant === 'success'"
            class="mt-0.5 h-5 w-5 shrink-0"
        />
        <ExclamationTriangleIcon v-else class="mt-0.5 h-5 w-5 shrink-0" />
        {{ message }}
    </div>
</template>
