<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import RoomForm from '@/Pages/Hotels/Rooms/Components/RoomForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    hotel: Object,
    room: Object,
    roomTypes: Array,
    currency: String,
});
const { t } = useI18n();
</script>

<template>
    <Head :title="t('rooms.pages.edit.heading', { number: room.number })" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="
                        t('rooms.pages.edit.heading', { number: room.number })
                    "
                    :description="t('rooms.pages.edit.description')"
                />
                <RoomForm
                    mode="edit"
                    :hotel="hotel"
                    :room="room"
                    :room-types="roomTypes"
                    :currency="currency"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
