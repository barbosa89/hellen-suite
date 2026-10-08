<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import ReservationForm from '@/Pages/Hotels/Reservations/Components/ReservationForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    hotel: Object,
    reservation: Object,
    identificationTypes: Array,
    currency: String,
    countries: { type: Array, default: () => [] },
    subdivisions: { type: Array, default: () => [] },
});
const { t } = useI18n();
</script>

<template>
    <Head :title="t('reservations.pages.edit.heading')" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('reservations.pages.edit.heading')"
                    :description="t('reservations.pages.edit.description')"
                />
                <ReservationForm
                    :hotel="hotel"
                    :reservation="reservation"
                    :identification-types="identificationTypes"
                    :currency="currency"
                    :countries="countries"
                    :subdivisions="subdivisions"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
