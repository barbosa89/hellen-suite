<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import UploadInput from '@/Components/UploadInput.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    show: Boolean,
    mode: String,
    folio: Object,
    hotel: Object,
    stay: Object,
});
const emit = defineEmits(['close']);
const { t, locale } = useI18n();
const form = useForm({
    amount: '',
    method: 'cash',
    comment: '',
    support: null,
    description: '',
    type: 'courtesy',
    reason: '',
});
const title = computed(() =>
    props.mode ? t(`payments.dialogs.${props.mode}_title`) : '',
);

function money(minor, currency) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(Number(minor) / 100);
}

watch(
    () => props.show,
    (show) => {
        if (show) {
            form.reset();
            form.clearErrors();
            form.amount =
                props.mode !== 'charge' && props.folio
                    ? String(Math.max(0, props.folio.balance_minor) / 100)
                    : '';
        }
    },
);

watch(
    () => form.method,
    (method) => {
        if (method !== 'bank_transfer') {
            form.support = null;
            form.clearErrors('support');
        }
    },
);

function submit() {
    const base = [props.hotel.id, props.stay.id, props.folio.id];
    const routeName =
        props.mode === 'payment'
            ? 'hotels.stays.folios.payments.store'
            : props.mode === 'charge'
              ? 'hotels.stays.folios.charges.store'
              : 'hotels.stays.folios.adjustments.store';
    form.post(route(routeName, base), {
        forceFormData: props.mode === 'payment',
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
}
</script>

<template>
    <Modal
        :show="show"
        max-width="lg"
        :closeable="!form.processing"
        aria-labelledby="financial-action-title"
        @close="emit('close')"
    >
        <form class="grid gap-5 p-6 sm:p-7" @submit.prevent="submit">
            <div
                class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start"
            >
                <h2
                    id="financial-action-title"
                    class="text-xl font-semibold text-neutral-950 dark:text-white"
                >
                    {{ title }}
                </h2>
                <div class="sm:text-end">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        {{ folio?.label }}
                    </p>
                    <p
                        v-if="folio"
                        class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                    >
                        {{ t('payments.summary.balance') }}:
                        {{ money(folio.balance_minor, folio.currency) }}
                    </p>
                </div>
            </div>

            <div v-if="mode === 'charge'" class="grid gap-2">
                <InputLabel
                    for="financial-description"
                    :value="t('payments.fields.description')"
                    required
                />
                <TextInput
                    id="financial-description"
                    v-model="form.description"
                    required
                    autofocus
                    aria-describedby="financial-description-error"
                    :aria-invalid="Boolean(form.errors.description)"
                    :invalid="Boolean(form.errors.description)"
                />
                <InputError
                    id="financial-description-error"
                    :message="form.errors.description"
                />
            </div>

            <div v-if="mode === 'adjustment'" class="grid gap-2">
                <InputLabel
                    for="financial-type"
                    :value="t('payments.fields.type')"
                    required
                />
                <select
                    id="financial-type"
                    v-model="form.type"
                    aria-describedby="financial-type-error"
                    :aria-invalid="Boolean(form.errors.type)"
                    class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                    :class="
                        form.errors.type
                            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                            : 'border-neutral-300 dark:border-neutral-700'
                    "
                >
                    <option value="courtesy">
                        {{ t('payments.adjustments.courtesy') }}
                    </option>
                    <option value="write_off">
                        {{ t('payments.adjustments.write_off') }}
                    </option>
                    <option value="discount">
                        {{ t('payments.adjustments.discount') }}
                    </option>
                </select>
                <InputError
                    id="financial-type-error"
                    :message="form.errors.type"
                />
            </div>

            <div class="grid gap-2">
                <InputLabel
                    for="financial-amount"
                    :value="t('payments.fields.amount')"
                    required
                />
                <div class="relative">
                    <TextInput
                        id="financial-amount"
                        v-model="form.amount"
                        type="number"
                        min="0.01"
                        step="0.01"
                        inputmode="decimal"
                        class="w-full pe-16 tabular-nums"
                        required
                        :autofocus="mode !== 'charge'"
                        aria-describedby="financial-amount-error"
                        :aria-invalid="Boolean(form.errors.amount)"
                        :invalid="Boolean(form.errors.amount)"
                    />
                    <span
                        class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs font-semibold text-neutral-500"
                        >{{ folio?.currency }}</span
                    >
                </div>
                <InputError
                    id="financial-amount-error"
                    :message="form.errors.amount"
                />
            </div>

            <template v-if="mode === 'payment'">
                <div class="grid gap-2">
                    <InputLabel
                        for="payment-method"
                        :value="t('payments.fields.method')"
                        required
                    />
                    <select
                        id="payment-method"
                        v-model="form.method"
                        aria-describedby="payment-method-error"
                        :aria-invalid="Boolean(form.errors.method)"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                        :class="
                            form.errors.method
                                ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                                : 'border-neutral-300 dark:border-neutral-700'
                        "
                    >
                        <option value="cash">
                            {{ t('payments.methods.cash') }}
                        </option>
                        <option value="bank_transfer">
                            {{ t('payments.methods.bank_transfer') }}
                        </option>
                    </select>
                    <InputError
                        id="payment-method-error"
                        :message="form.errors.method"
                    />
                </div>
                <div v-if="form.method === 'bank_transfer'" class="grid gap-2">
                    <InputLabel
                        for="financial-support"
                        :value="t('payments.fields.support')"
                    />
                    <UploadInput
                        id="financial-support"
                        v-model="form.support"
                        accept="image/jpeg,image/png,image/webp"
                        :title="t('payments.fields.support')"
                        :hint="t('payments.fields.support_hint')"
                        :invalid="Boolean(form.errors.support)"
                    />
                    <InputError :message="form.errors.support" />
                </div>
            </template>

            <div class="grid gap-2">
                <InputLabel
                    :for="
                        mode === 'adjustment'
                            ? 'financial-reason'
                            : 'financial-comment'
                    "
                    :value="
                        mode === 'adjustment'
                            ? t('payments.fields.reason')
                            : t('payments.fields.comment')
                    "
                    :required="mode === 'adjustment'"
                />
                <textarea
                    v-if="mode === 'adjustment'"
                    id="financial-reason"
                    v-model="form.reason"
                    rows="3"
                    maxlength="255"
                    required
                    :aria-invalid="Boolean(form.errors.reason)"
                    aria-describedby="financial-comment-error"
                    class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                    :class="
                        form.errors.reason
                            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                            : 'border-neutral-300 dark:border-neutral-700'
                    "
                />
                <textarea
                    v-else
                    id="financial-comment"
                    v-model="form.comment"
                    rows="3"
                    maxlength="255"
                    :aria-invalid="Boolean(form.errors.comment)"
                    aria-describedby="financial-comment-error"
                    class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                    :class="
                        form.errors.comment
                            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                            : 'border-neutral-300 dark:border-neutral-700'
                    "
                />
                <InputError
                    id="financial-comment-error"
                    :message="
                        form.errors.reason ??
                        form.errors.comment ??
                        form.errors.payment ??
                        form.errors.adjustment ??
                        form.errors.charge
                    "
                />
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <SecondaryButton
                    type="button"
                    :disabled="form.processing"
                    @click="emit('close')"
                    >{{ t('app.cancel') }}</SecondaryButton
                >
                <PrimaryButton type="submit" :disabled="form.processing">{{
                    t(
                        `payments.actions.${mode === 'payment' ? 'record_payment' : mode === 'charge' ? 'add_charge' : 'apply_adjustment'}`,
                    )
                }}</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
