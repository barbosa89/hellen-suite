<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) =>
            ['primary', 'secondary', 'ghost', 'danger'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md'].includes(value),
    },
    prefetch: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-lg border font-semibold transition-[background-color,border-color,color,box-shadow] duration-150 ease-out focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:outline-hidden motion-reduce:transition-none dark:focus-visible:ring-offset-neutral-950',
    props.size === 'sm'
        ? 'min-h-11 px-3 py-1.5 text-sm sm:min-h-9'
        : 'min-h-11 px-4 py-2 text-sm',
    {
        primary:
            'border-primary-800 bg-primary-800 text-white shadow-sm hover:border-primary-900 hover:bg-primary-900 active:border-primary-900 active:bg-primary-900 dark:border-primary-400 dark:bg-primary-400 dark:text-neutral-950 dark:hover:border-primary-300 dark:hover:bg-primary-300 dark:active:border-primary-300 dark:active:bg-primary-300',
        secondary:
            'border-neutral-300 bg-white text-neutral-700 shadow-sm hover:border-neutral-400 hover:bg-neutral-50 hover:text-neutral-950 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:border-neutral-600 dark:hover:bg-neutral-800 dark:hover:text-white',
        ghost: 'border-transparent bg-transparent text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white',
        danger: 'border-danger-200 bg-white text-danger-700 hover:border-danger-300 hover:bg-danger-50 dark:border-danger-900 dark:bg-neutral-900 dark:text-danger-300 dark:hover:bg-danger-900/30',
    }[props.variant],
]);
</script>

<template>
    <Link :href="href" :prefetch="prefetch" :class="classes">
        <slot />
    </Link>
</template>
