<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import RoomTypeForm from '@/Pages/Hotels/Rooms/Components/RoomTypeForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({ hotel: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('room_types.pages.create.heading')" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('room_types.pages.create.heading')"
                    :description="t('room_types.pages.create.description')"
                />
                <RoomTypeForm mode="create" :hotel="hotel" />
            </div>
        </div>
    </DefaultLayout>
</template>
