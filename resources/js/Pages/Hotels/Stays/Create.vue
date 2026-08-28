<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import StayForm from '@/Pages/Hotels/Stays/Components/StayForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    hotel: Object,
    rooms: Array,
    identificationTypes: Array,
    currency: String,
});
const { t } = useI18n();
</script>

<template>
    <Head :title="t('stays.pages.create.heading')" />

    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('stays.pages.create.heading')"
                    :description="t('stays.pages.create.description')"
                />
                <StayForm
                    :hotel="hotel"
                    :rooms="rooms"
                    :identification-types="identificationTypes"
                    :currency="currency"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
