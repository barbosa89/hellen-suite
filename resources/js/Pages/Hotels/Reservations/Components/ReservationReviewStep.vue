<script setup>
import ReservationStepPanel from '@/Pages/Hotels/Reservations/Components/ReservationStepPanel.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    guests: { type: Array, required: true },
    rooms: { type: Array, required: true },
    reservedRooms: { type: Array, required: true },
});

const { t } = useI18n();

function assignedRoomId(guest) {
    return (
        props.reservedRooms.find((room) => room.guest_keys.includes(guest.key))
            ?.room_id ?? ''
    );
}

function guestName(guest) {
    return (
        `${guest.first_name} ${guest.last_name}`.trim() ||
        t('reservations.form.guests.unnamed')
    );
}

function assignedRoomNumber(guest) {
    return props.rooms.find(
        (room) => Number(room.id) === Number(assignedRoomId(guest)),
    )?.number;
}
</script>

<template>
    <ReservationStepPanel
        :title="t('reservations.form.review.title')"
        :description="t('reservations.form.review.description')"
    >
        <div class="grid gap-7 p-5 sm:p-7">
            <div class="grid gap-3 sm:grid-cols-2">
                <div
                    v-for="guest in guests"
                    :key="guest.key"
                    class="rounded-xl border border-neutral-200 px-4 py-3 dark:border-neutral-800"
                >
                    <p class="font-semibold text-neutral-950 dark:text-white">
                        {{ guestName(guest) }}
                    </p>
                    <p
                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                    >
                        {{ assignedRoomNumber(guest) }}
                    </p>
                </div>
            </div>
            <div
                class="border-t border-neutral-200 pt-6 dark:border-neutral-800"
            >
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    {{ t('reservations.form.review.draft_note') }}
                </p>
            </div>
        </div>
    </ReservationStepPanel>
</template>
