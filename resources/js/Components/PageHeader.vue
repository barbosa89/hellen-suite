<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <header
        class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"
    >
        <div class="min-w-0">
            <div v-if="$slots.leading" class="mb-4">
                <slot name="leading" />
            </div>
            <h1
                class="text-3xl font-semibold tracking-[-0.025em] text-neutral-950 sm:text-4xl dark:text-white"
            >
                {{ title }}
            </h1>
            <p
                v-if="description"
                class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 sm:text-base dark:text-neutral-400"
            >
                {{ description }}
            </p>
            <div v-if="$slots.meta" class="mt-4">
                <slot name="meta" />
            </div>
        </div>

        <div
            v-if="$slots.actions"
            class="flex shrink-0 flex-wrap items-center gap-2"
        >
            <slot name="actions" />
        </div>
    </header>
</template>
