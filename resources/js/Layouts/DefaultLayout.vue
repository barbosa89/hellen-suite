<script setup>
import { ref } from 'vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleDropdown from '@/Components/LocaleDropdown.vue';
import Sidebar from '@/Components/Sidebar.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const sidebarOpen = ref(true);

const props = defineProps({
    hotel: {
        type: Object,
        default: null,
    },
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div>
        <Sidebar
            :open="sidebarOpen"
            :hotel="props.hotel"
            @toggle="toggleSidebar"
        />

        <div
            :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-0'"
            class="min-h-screen bg-neutral-100 transition-[padding] duration-300 ease-in-out dark:bg-neutral-950"
        >
            <nav
                class="border-b border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        class="focus:ring-primary-500 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-neutral-500 transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-700 focus:ring-2 focus:outline-hidden dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100"
                        :aria-label="
                            sidebarOpen ? 'Ocultar menú' : 'Mostrar menú'
                        "
                        @click="toggleSidebar"
                    >
                        <XMarkIcon v-if="sidebarOpen" class="h-5 w-5" />
                        <Bars3Icon v-else class="h-5 w-5" />
                    </button>

                    <Link
                        :href="route('dashboard')"
                        class="flex shrink-0 items-center lg:hidden"
                    >
                        <ApplicationLogo
                            class="text-primary-600 block h-8 w-auto fill-current"
                        />
                    </Link>

                    <div class="ms-auto flex items-center gap-3">
                        <ThemeToggle />
                        <LocaleDropdown />
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow-sm dark:bg-neutral-900"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
