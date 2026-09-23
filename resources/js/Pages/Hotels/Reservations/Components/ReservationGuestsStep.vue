<script setup>
import SecondaryButton from '@/Components/SecondaryButton.vue';
import GuestFields from '@/Pages/Hotels/Guests/Components/GuestFields.vue';
import GuestLookupField from '@/Pages/Hotels/Guests/Components/GuestLookupField.vue';
import ReservationStepPanel from '@/Pages/Hotels/Reservations/Components/ReservationStepPanel.vue';
import { UserPlusIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

defineProps({
    hotel: { type: Object, required: true },
    guests: { type: Array, required: true },
    responsibleGuestKey: { type: String, required: true },
    identificationTypes: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
});

const emit = defineEmits([
    'add-guest',
    'remove-guest',
    'select-guest',
    'reset-guest',
    'update-guest',
]);
const { t } = useI18n();
</script>

<template>
    <ReservationStepPanel
        :title="t('reservations.form.guests.title')"
        :description="t('reservations.form.guests.description')"
    >
        <div class="grid gap-5 p-5 sm:p-7">
            <article
                v-for="(entry, index) in guests"
                :key="entry.key"
                class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-800"
            >
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-neutral-950 dark:text-white">
                        {{
                            entry.key === responsibleGuestKey
                                ? t('reservations.form.guests.responsible')
                                : `${t('reservations.form.guests.companion')} ${index}`
                        }}
                    </h3>
                    <div class="flex items-center gap-2">
                        <button
                            v-if="entry.guest_id"
                            type="button"
                            class="text-primary-700 dark:text-primary-300 text-sm font-semibold"
                            @click="emit('reset-guest', index)"
                        >
                            {{ t('reservations.form.guests.new') }}
                        </button>
                        <button
                            v-if="entry.key !== responsibleGuestKey"
                            type="button"
                            class="text-danger-700 dark:text-danger-300 inline-flex h-11 w-11 items-center justify-center rounded-lg"
                            :aria-label="t('app.remove')"
                            @click="emit('remove-guest', index)"
                        >
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                <GuestLookupField
                    v-if="!entry.guest_id"
                    class="mb-5"
                    :hotel="hotel"
                    :id="`reservation-search-${entry.key}`"
                    @select="emit('select-guest', index, $event)"
                />
                <GuestFields
                    :guest="entry"
                    :identification-types="identificationTypes"
                    :errors="errors"
                    :prefix="`guests.${index}`"
                    :id-prefix="`reservation-${entry.key}`"
                    :disabled="Boolean(entry.guest_id)"
                    @update:guest="emit('update-guest', index, $event)"
                />
            </article>
            <SecondaryButton
                type="button"
                class="justify-center"
                @click="emit('add-guest')"
                ><UserPlusIcon class="h-4 w-4" />{{
                    t('reservations.form.guests.add')
                }}</SecondaryButton
            >
        </div>
    </ReservationStepPanel>
</template>
