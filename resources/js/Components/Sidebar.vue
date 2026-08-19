<script setup>
import { Link } from '@inertiajs/vue3';
import {
    BuildingStorefrontIcon,
    ChevronDoubleLeftIcon,
    HomeIcon,
} from '@heroicons/vue/24/outline';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    open: {
        type: Boolean,
        required: true,
    },
});

defineEmits(['toggle']);

const navigation = [
    {
        name: 'Dashboard',
        href: () => route('dashboard'),
        active: () => route().current('dashboard'),
        icon: HomeIcon,
    },
    {
        name: 'Hoteles',
        href: () => route('hotels.index'),
        active: () => route().current('hotels.*'),
        icon: BuildingStorefrontIcon,
    },
];
</script>

<template>
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 start-0 z-40 flex w-64 flex-col border-e border-neutral-200 bg-white transition-transform duration-300 ease-in-out dark:border-neutral-800 dark:bg-neutral-900"
    >
        <Link
            :href="route('dashboard')"
            class="flex h-16 items-center gap-2.5 px-5"
        >
            <ApplicationLogo
                class="text-primary-600 block h-8 w-auto fill-current"
            />
            <span
                class="text-lg font-bold tracking-tight text-neutral-800 dark:text-neutral-100"
            >
                Hellen
                <span class="text-primary-600 dark:text-primary-400"
                    >Suite</span
                >
            </span>
        </Link>

        <nav class="mt-2 flex-1 space-y-1 overflow-y-auto px-3 pb-4">
            <template v-for="item in navigation" :key="item.name">
                <Link
                    :href="item.href()"
                    :class="
                        item.active()
                            ? 'bg-primary-50 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300'
                            : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100'
                    "
                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition duration-150 ease-in-out"
                >
                    <component
                        :is="item.icon"
                        :class="
                            item.active()
                                ? 'text-primary-600 dark:text-primary-400'
                                : 'text-neutral-400 group-hover:text-neutral-600 dark:text-neutral-500 dark:group-hover:text-neutral-300'
                        "
                        class="h-5 w-5 shrink-0"
                    />
                    {{ item.name }}
                </Link>
            </template>
        </nav>

        <div class="border-t border-neutral-200 p-3 dark:border-neutral-800">
            <button
                type="button"
                class="focus:ring-primary-500 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-neutral-500 transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-700 focus:ring-2 focus:outline-hidden dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100"
                @click="$emit('toggle')"
            >
                <ChevronDoubleLeftIcon class="h-5 w-5 shrink-0" />
                Ocultar menú
            </button>
        </div>
    </aside>

    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="open"
            class="fixed inset-0 z-30 bg-neutral-900/50 lg:hidden"
            @click="$emit('toggle')"
        />
    </Transition>
</template>
