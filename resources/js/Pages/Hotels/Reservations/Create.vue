<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import ReservationForm from '@/Pages/Hotels/Reservations/Components/ReservationForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({ hotel: Object, identificationTypes: Array, currency: String });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('reservations.pages.create.heading')" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('reservations.pages.create.heading')"
                    :description="t('reservations.pages.create.description')"
                />
                <ReservationForm
                    :hotel="hotel"
                    :identification-types="identificationTypes"
                    :currency="currency"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
