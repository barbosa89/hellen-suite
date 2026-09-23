<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ReservationStepPanel from '@/Pages/Hotels/Reservations/Components/ReservationStepPanel.vue';
import { useI18n } from 'vue-i18n';

defineProps({
    errors: { type: Object, default: () => ({}) },
});

const plannedCheckInOn = defineModel('plannedCheckInOn', {
    type: String,
    required: true,
});
const plannedCheckOutOn = defineModel('plannedCheckOutOn', {
    type: String,
    required: true,
});
const { t } = useI18n();
</script>

<template>
    <ReservationStepPanel
        :title="t('reservations.form.dates.title')"
        :description="t('reservations.form.dates.description')"
    >
        <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
            <div class="grid gap-2">
                <InputLabel
                    for="planned-check-in"
                    :value="t('reservations.fields.check_in')"
                    required
                />
                <TextInput
                    id="planned-check-in"
                    v-model="plannedCheckInOn"
                    type="date"
                />
                <InputError :message="errors.planned_check_in_on" />
            </div>
            <div class="grid gap-2">
                <InputLabel
                    for="planned-check-out"
                    :value="t('reservations.fields.check_out')"
                    required
                />
                <TextInput
                    id="planned-check-out"
                    v-model="plannedCheckOutOn"
                    type="date"
                />
                <InputError :message="errors.planned_check_out_on" />
            </div>
        </div>
    </ReservationStepPanel>
</template>
