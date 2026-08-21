<script setup>
import { computed } from 'vue';

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
    },
});

const initials = computed(() =>
    props.hotel.business_name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase(),
);

const sizeClass = computed(
    () =>
        ({
            sm: 'h-9 w-9 rounded-lg text-xs',
            md: 'h-14 w-14 rounded-xl text-base',
            lg: 'h-20 w-20 rounded-xl text-xl',
            xl: 'h-28 w-28 rounded-2xl text-2xl',
        })[props.size],
);
</script>

<template>
    <div
        class="bg-primary-50 text-primary-700 ring-primary-200 dark:bg-primary-950 dark:text-primary-300 dark:ring-primary-800 relative flex shrink-0 items-center justify-center overflow-hidden font-semibold ring-1"
        :class="sizeClass"
    >
        <img
            v-if="hotel.image"
            :src="hotel.image"
            :alt="hotel.business_name"
            class="h-full w-full object-cover"
            decoding="async"
        />
        <span v-else aria-hidden="true">{{ initials }}</span>
    </div>
</template>
