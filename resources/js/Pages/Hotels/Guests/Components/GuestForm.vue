<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import GuestFields from '@/Pages/Hotels/Guests/Components/GuestFields.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { CheckIcon, ChevronLeftIcon } from '@heroicons/vue/24/outline';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: { type: Object, required: true },
    guest: { type: Object, default: null },
    identificationTypes: { type: Array, required: true },
    countries: { type: Array, default: () => [] },
});

const { t } = useI18n();
const isEditing = computed(() => props.guest !== null);
const form = useForm({
    first_name: props.guest?.first_name ?? '',
    second_first_name: props.guest?.second_first_name ?? '',
    last_name: props.guest?.last_name ?? '',
    second_last_name: props.guest?.second_last_name ?? '',
    identification_type_id: props.guest?.identification_type_id ?? '',
    identification_number: props.guest?.identification_number ?? '',
    birth_date: props.guest?.birth_date ?? '',
    gender: props.guest?.gender ?? '',
    nationality: props.guest?.nationality ?? '',
    residence_country: props.guest?.residence_country ?? '',
    mobile: props.guest?.mobile ?? '',
    email: props.guest?.email ?? '',
});

function submit() {
    if (isEditing.value) {
        form.put(
            route('hotels.guests.update', [props.hotel.id, props.guest.id]),
        );
        return;
    }

    form.post(route('hotels.guests.store', props.hotel.id));
}
</script>

<template>
    <form
        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
        @submit.prevent="submit"
    >
        <div class="grid divide-y divide-neutral-200 dark:divide-neutral-800">
            <section class="p-5 sm:p-7 lg:p-8">
                <div class="mb-6">
                    <h2
                        class="text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('guests.form.identity.title') }}
                    </h2>
                    <p
                        class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('guests.form.identity.description') }}
                    </p>
                </div>
                <GuestFields
                    :guest="form"
                    :identification-types="identificationTypes"
                    :countries="countries"
                    :errors="form.errors"
                    @update:guest="Object.assign(form, $event)"
                />
            </section>
        </div>
        <footer
            class="flex flex-col-reverse gap-3 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
        >
            <ActionLink
                :href="route('hotels.guests.index', hotel.id)"
                variant="ghost"
                ><ChevronLeftIcon class="h-4 w-4" />{{
                    t('app.cancel')
                }}</ActionLink
            >
            <PrimaryButton type="submit" :disabled="form.processing"
                ><CheckIcon class="h-4 w-4" />{{
                    form.processing ? t('app.saving') : t('app.save')
                }}</PrimaryButton
            >
        </footer>
    </form>
</template>
