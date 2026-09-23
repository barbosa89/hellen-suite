<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, required: true },
});
</script>

<template>
    <section
        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
    >
        <div
            class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
        >
            <h2 class="text-lg font-semibold text-neutral-950 dark:text-white">
                {{ title }}
            </h2>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                {{ description }}
            </p>
        </div>
        <slot />
    </section>
</template>
