<script setup>
import HotelLayout from '@/Layouts/HotelLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <HotelLayout>
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="p-6 text-neutral-900 sm:p-7 dark:text-neutral-100"
                    >
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </HotelLayout>
</template>
