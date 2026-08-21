<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="flex min-h-11 w-full items-center px-4 py-2 text-start text-sm leading-5 font-medium text-neutral-700 transition-colors duration-150 hover:bg-neutral-100 focus:bg-neutral-100 focus:outline-hidden motion-reduce:transition-none dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
    >
        <slot />
    </Link>
</template>
