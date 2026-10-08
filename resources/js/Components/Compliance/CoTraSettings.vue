<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from 'vue-i18n';

defineProps({
    errors: { type: Object, default: () => ({}) },
    configured: {
        type: Boolean,
        default: false,
    },
});

const complianceEnabled = defineModel('complianceEnabled', {
    type: Boolean,
    default: false,
});
const establishmentCode = defineModel('establishmentCode', {
    type: String,
    default: '',
});
const credential = defineModel('credential', {
    type: String,
    default: '',
});

const { t } = useI18n();
</script>

<template>
    <div class="grid gap-5">
        <div
            class="rounded-xl bg-neutral-50 p-4 text-sm leading-6 text-neutral-600 dark:bg-neutral-950/60 dark:text-neutral-400"
        >
            <p class="font-semibold text-neutral-950 dark:text-white">
                {{ t('hotels.form.compliance.co_tra_title') }}
            </p>
            <p class="mt-1">
                {{ t('hotels.form.compliance.co_tra_description') }}
            </p>
        </div>

        <div>
            <label class="flex items-start gap-3">
                <Checkbox
                    v-model:checked="complianceEnabled"
                    name="compliance_enabled"
                    class="mt-1"
                />
                <span
                    class="text-sm font-medium text-neutral-950 dark:text-neutral-100"
                >
                    {{ t('hotels.fields.compliance_enabled.label') }}
                </span>
            </label>
            <InputError :message="errors.compliance_enabled" />
        </div>

        <div class="grid content-start gap-2">
            <InputLabel
                for="establishment-code"
                :value="t('hotels.fields.establishment_code.label')"
            />
            <TextInput
                id="establishment-code"
                v-model="establishmentCode"
                name="establishment_code"
                type="text"
                class="w-full tabular-nums"
                autocomplete="off"
                :invalid="Boolean(errors.establishment_code)"
                :aria-invalid="Boolean(errors.establishment_code)"
                aria-describedby="establishment-code-error"
            />
            <InputError
                id="establishment-code-error"
                :message="errors.establishment_code"
            />
        </div>

        <div class="grid content-start gap-2">
            <div class="flex items-center justify-between gap-3">
                <InputLabel
                    for="credential"
                    :value="t('hotels.fields.credential.label')"
                />
                <span
                    class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                    :class="
                        configured
                            ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                            : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400'
                    "
                >
                    {{
                        configured
                            ? t('hotels.form.compliance.co_tra_configured')
                            : t('hotels.form.compliance.co_tra_not_configured')
                    }}
                </span>
            </div>
            <TextInput
                id="credential"
                v-model="credential"
                name="credential"
                type="password"
                class="w-full"
                autocomplete="new-password"
                :placeholder="configured ? '••••••••' : ''"
                :invalid="Boolean(errors.credential)"
                :aria-invalid="Boolean(errors.credential)"
                aria-describedby="credential-error credential-help"
            />
            <p
                id="credential-help"
                class="text-xs leading-5 text-neutral-600 dark:text-neutral-400"
            >
                {{ t('hotels.form.compliance.co_tra_token_hint') }}
            </p>
            <InputError id="credential-error" :message="errors.credential" />
        </div>
    </div>
</template>
