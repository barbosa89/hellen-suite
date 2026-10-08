<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-neutral-50 pt-6 sm:justify-center sm:pt-0 dark:bg-neutral-950"
    >
        <div>
            <Link href="/">
                <ApplicationLogo
                    class="text-primary-600 h-20 w-20 fill-current"
                />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden rounded-2xl bg-white px-6 py-4 shadow-sm sm:max-w-md dark:bg-neutral-900"
        >
            <slot />
        </div>
    </div>
</template>
