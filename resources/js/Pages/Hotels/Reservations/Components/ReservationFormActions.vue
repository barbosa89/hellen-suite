<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {
    CheckIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

defineProps({
    currentStep: { type: Number, required: true },
    processing: { type: Boolean, default: false },
});

const emit = defineEmits(['next', 'previous']);
const { t } = useI18n();
</script>

<template>
    <footer
        class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <SecondaryButton
            v-if="currentStep > 1"
            type="button"
            @click="emit('previous')"
            ><ChevronLeftIcon class="h-4 w-4" />{{
                t('reservations.actions.back')
            }}</SecondaryButton
        ><span v-else />
        <PrimaryButton
            v-if="currentStep < 4"
            type="button"
            @click="emit('next')"
            >{{ t('reservations.actions.continue')
            }}<ChevronRightIcon class="h-4 w-4"
        /></PrimaryButton>
        <PrimaryButton v-else type="submit" :disabled="processing"
            ><CheckIcon class="h-4 w-4" />{{
                processing
                    ? t('app.saving')
                    : t('reservations.actions.save_draft')
            }}</PrimaryButton
        >
    </footer>
</template>
