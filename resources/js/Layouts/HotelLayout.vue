<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleDropdown from '@/Components/LocaleDropdown.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
</script>

<template>
    <div class="min-h-screen bg-neutral-100 dark:bg-neutral-950">
        <nav
            class="border-b border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900"
        >
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <Link :href="route('dashboard')" class="flex items-center">
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

        <header
            v-if="$slots.header"
            class="bg-white shadow-sm dark:bg-neutral-900"
        >
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
