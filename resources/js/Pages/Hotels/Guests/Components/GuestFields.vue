<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    guest: { type: Object, required: true },
    identificationTypes: { type: Array, required: true },
    countries: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
    prefix: { type: String, default: '' },
    idPrefix: { type: String, default: 'guest' },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:guest']);
const { t } = useI18n();
const field = (name) => (props.prefix ? `${props.prefix}.${name}` : name);
const error = (name) => computed(() => props.errors[field(name)]);
const fieldValue = (name) =>
    computed({
        get: () => props.guest[name],
        set: (value) => emit('update:guest', { ...props.guest, [name]: value }),
    });
</script>

<template>
    <div class="grid gap-5 md:grid-cols-2">
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-first-name`"
                :value="t('guests.fields.first_name.label')"
                required
            />
            <TextInput
                :id="`${idPrefix}-first-name`"
                v-model="fieldValue('first_name').value"
                class="w-full"
                :disabled="disabled"
                :invalid="Boolean(error('first_name').value)"
            />
            <InputError :message="error('first_name').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-last-name`"
                :value="t('guests.fields.last_name.label')"
                required
            />
            <TextInput
                :id="`${idPrefix}-last-name`"
                v-model="fieldValue('last_name').value"
                class="w-full"
                :disabled="disabled"
                :invalid="Boolean(error('last_name').value)"
            />
            <InputError :message="error('last_name').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-second-first-name`"
                :value="t('guests.fields.second_first_name.label')"
            />
            <TextInput
                :id="`${idPrefix}-second-first-name`"
                v-model="fieldValue('second_first_name').value"
                class="w-full"
                :disabled="disabled"
                :invalid="Boolean(error('second_first_name').value)"
            />
            <InputError :message="error('second_first_name').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-second-last-name`"
                :value="t('guests.fields.second_last_name.label')"
            />
            <TextInput
                :id="`${idPrefix}-second-last-name`"
                v-model="fieldValue('second_last_name').value"
                class="w-full"
                :disabled="disabled"
                :invalid="Boolean(error('second_last_name').value)"
            />
            <InputError :message="error('second_last_name').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-identification-type`"
                :value="t('guests.fields.identification_type.label')"
                required
            />
            <select
                :id="`${idPrefix}-identification-type`"
                v-model="fieldValue('identification_type_id').value"
                :disabled="disabled"
                class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden disabled:cursor-not-allowed disabled:opacity-60 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
            >
                <option value="" disabled>
                    {{ t('guests.form.select_identification_type') }}
                </option>
                <option
                    v-for="type in identificationTypes"
                    :key="type.id"
                    :value="type.id"
                >
                    {{ t(`identification_types.${type.code.toLowerCase()}`) }}
                </option>
            </select>
            <InputError :message="error('identification_type_id').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-identification-number`"
                :value="t('guests.fields.identification_number.label')"
                required
            />
            <TextInput
                :id="`${idPrefix}-identification-number`"
                v-model="fieldValue('identification_number').value"
                class="w-full tabular-nums"
                autocomplete="off"
                :disabled="disabled"
                :invalid="Boolean(error('identification_number').value)"
            />
            <InputError :message="error('identification_number').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-birth-date`"
                :value="t('guests.fields.birth_date.label')"
            />
            <TextInput
                :id="`${idPrefix}-birth-date`"
                v-model="fieldValue('birth_date').value"
                type="date"
                class="w-full"
                :disabled="disabled"
                :invalid="Boolean(error('birth_date').value)"
            />
            <InputError :message="error('birth_date').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-gender`"
                :value="t('guests.fields.gender.label')"
            />
            <select
                :id="`${idPrefix}-gender`"
                v-model="fieldValue('gender').value"
                :disabled="disabled"
                class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden disabled:cursor-not-allowed disabled:opacity-60 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
            >
                <option value="">
                    {{ t('app.not_provided') }}
                </option>
                <option value="F">
                    {{ t('guests.fields.gender.feminine') }}
                </option>
                <option value="M">
                    {{ t('guests.fields.gender.masculine') }}
                </option>
            </select>
            <InputError :message="error('gender').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-nationality`"
                :value="t('guests.fields.nationality.label')"
            />
            <select
                :id="`${idPrefix}-nationality`"
                v-model="fieldValue('nationality').value"
                :disabled="disabled"
                class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden disabled:cursor-not-allowed disabled:opacity-60 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
            >
                <option value="">
                    {{ t('app.not_provided') }}
                </option>
                <option
                    v-for="country in countries"
                    :key="country.code"
                    :value="country.code"
                >
                    {{ country.code }} — {{ country.name }}
                </option>
            </select>
            <InputError :message="error('nationality').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-residence-country`"
                :value="t('guests.fields.residence_country.label')"
            />
            <select
                :id="`${idPrefix}-residence-country`"
                v-model="fieldValue('residence_country').value"
                :disabled="disabled"
                class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden disabled:cursor-not-allowed disabled:opacity-60 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
            >
                <option value="">
                    {{ t('app.not_provided') }}
                </option>
                <option
                    v-for="country in countries"
                    :key="country.code"
                    :value="country.code"
                >
                    {{ country.code }} — {{ country.name }}
                </option>
            </select>
            <InputError :message="error('residence_country').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-mobile`"
                :value="t('guests.fields.mobile.label')"
            />
            <TextInput
                :id="`${idPrefix}-mobile`"
                v-model="fieldValue('mobile').value"
                type="tel"
                class="w-full"
                autocomplete="tel"
                :disabled="disabled"
                :invalid="Boolean(error('mobile').value)"
            />
            <InputError :message="error('mobile').value" />
        </div>
        <div class="grid content-start gap-2">
            <InputLabel
                :for="`${idPrefix}-email`"
                :value="t('guests.fields.email.label')"
            />
            <TextInput
                :id="`${idPrefix}-email`"
                v-model="fieldValue('email').value"
                type="email"
                class="w-full"
                autocomplete="email"
                :disabled="disabled"
                :invalid="Boolean(error('email').value)"
            />
            <InputError :message="error('email').value" />
        </div>
    </div>
</template>
