<script setup>
import GuestForm from '@/Components/GuestForm.vue';
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
defineProps({ hotel: Object, guest: Object, identificationTypes: Array });
const { t } = useI18n();
</script>
<template>
    <Head :title="t('guests.pages.edit.heading')" /><DefaultLayout
        :hotel="hotel"
        ><div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('guests.pages.edit.heading')"
                    :description="t('guests.pages.edit.description')"
                /><GuestForm
                    :hotel="hotel"
                    :guest="guest"
                    :identification-types="identificationTypes"
                />
            </div></div
    ></DefaultLayout>
</template>
