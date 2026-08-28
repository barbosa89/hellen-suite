<script setup>
import { RectangleGroupIcon, SquaresPlusIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
    active: {
        type: String,
        required: true,
        validator: (value) => ['rooms', 'types'].includes(value),
    },
});

const { t } = useI18n();

const tabs = [
    {
        key: 'rooms',
        icon: RectangleGroupIcon,
        label: () => t('rooms.title'),
        href: () => route('hotels.rooms.index', props.hotel.id),
    },
    {
        key: 'types',
        icon: SquaresPlusIcon,
        label: () => t('room_types.title'),
        href: () => route('hotels.room-types.index', props.hotel.id),
    },
];
</script>

<template>
    <nav
        class="flex w-full gap-1 overflow-x-auto rounded-xl border border-neutral-200 bg-white p-1.5 dark:border-neutral-800 dark:bg-neutral-900"
        :aria-label="t('rooms.module_navigation')"
    >
        <Link
            v-for="tab in tabs"
            :key="tab.key"
            :href="tab.href()"
            class="focus-visible:ring-primary-500 inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden motion-reduce:transition-none sm:flex-none dark:focus-visible:ring-offset-neutral-950"
            :class="
                active === tab.key
                    ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                    : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white'
            "
        >
            <component :is="tab.icon" class="h-5 w-5 shrink-0" />
            {{ tab.label() }}
        </Link>
    </nav>
</template>
