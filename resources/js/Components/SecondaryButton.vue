<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="focus:ring-primary-500 inline-flex items-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold tracking-widest text-neutral-700 uppercase shadow-xs transition duration-150 ease-in-out hover:bg-neutral-50 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:opacity-25 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-950"
    >
        <slot />
    </button>
</template>
