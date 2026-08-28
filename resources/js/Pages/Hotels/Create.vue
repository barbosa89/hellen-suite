<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import HotelLayout from '@/Layouts/HotelLayout.vue';
import HotelForm from '@/Pages/Hotels/Components/HotelForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('hotels.pages.create.heading')" />

    <HotelLayout>
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="mx-auto grid w-full max-w-[100rem] gap-7">
                <PageHeader
                    :title="t('hotels.pages.create.heading')"
                    :description="t('hotels.pages.create.description')"
                />
                <HotelForm mode="create" />
            </div>
        </div>
    </HotelLayout>
</template>
