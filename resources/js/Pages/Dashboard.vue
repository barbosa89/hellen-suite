<script setup>
import HotelLayout from '@/Layouts/HotelLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <HotelLayout>
        <template #header>
            <h2
                class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-xs sm:rounded-lg dark:bg-neutral-800"
                >
                    <div class="p-6 text-neutral-900 dark:text-neutral-100">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </HotelLayout>
</template>
