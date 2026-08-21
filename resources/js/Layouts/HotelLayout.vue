<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleDropdown from '@/Components/LocaleDropdown.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950">
        <nav
            class="sticky top-0 z-30 border-b border-neutral-200 bg-white/95 dark:border-neutral-800 dark:bg-neutral-950/95"
        >
            <div
                class="flex h-16 w-full items-center gap-4 px-4 sm:px-6 lg:px-8 2xl:px-10"
            >
                <Link
                    :href="route('hotels.index')"
                    class="focus-visible:ring-primary-500 flex items-center gap-3 rounded-lg focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden dark:focus-visible:ring-offset-neutral-950"
                >
                    <ApplicationLogo
                        class="text-primary-600 block h-8 w-8 fill-current"
                    />
                    <span
                        class="text-lg font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white"
                    >
                        Hellen
                        <span class="text-primary-600 dark:text-primary-400"
                            >Suite</span
                        >
                    </span>
                </Link>

                <div
                    class="hidden h-6 w-px bg-neutral-200 sm:block dark:bg-neutral-800"
                    aria-hidden="true"
                />
                <span
                    class="hidden text-sm font-medium text-neutral-600 sm:block dark:text-neutral-400"
                >
                    {{ t('hotels.directory') }}
                </span>

                <div class="ms-auto flex items-center gap-2">
                    <ThemeToggle />
                    <LocaleDropdown />
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>
