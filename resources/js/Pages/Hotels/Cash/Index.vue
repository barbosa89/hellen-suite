<script setup>
import FlashMessage from '@/Components/FlashMessage.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowDownTrayIcon,
    ArrowUpTrayIcon,
    BanknotesIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    movements: Object,
    balanceMinor: Number,
    currency: String,
    flash: { type: Object, default: () => ({}) },
});
const { t, locale } = useI18n();
const isOpen = ref(false);
const form = useForm({ type: 'manual_entry', amount: '', comment: '' });

function money(minor, currency = props.currency) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(Number(minor) / 100);
}

function close() {
    if (!form.processing) {
        isOpen.value = false;
        form.reset();
        form.clearErrors();
    }
}

function open() {
    form.reset();
    form.clearErrors();
    isOpen.value = true;
}

function submit() {
    form.post(route('hotels.cash.store', props.hotel.id), {
        preserveScroll: true,
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
            form.clearErrors();
        },
    });
}
</script>

<template>
    <Head :title="t('cash.title')" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('cash.title')"
                    :description="
                        t('cash.description', { hotel: hotel.business_name })
                    "
                >
                    <template #actions
                        ><PrimaryButton type="button" @click="open"
                            ><PlusIcon class="h-4 w-4" />{{
                                t('cash.actions.record')
                            }}</PrimaryButton
                        ></template
                    >
                </PageHeader>
                <FlashMessage v-if="flash.success" :message="flash.success" />

                <section
                    class="overflow-hidden rounded-2xl bg-neutral-900 text-white shadow-sm dark:bg-white dark:text-neutral-950"
                >
                    <div
                        class="flex flex-col gap-5 px-5 py-6 sm:flex-row sm:items-end sm:justify-between sm:px-7 sm:py-7"
                    >
                        <div>
                            <div
                                class="flex items-center gap-2 text-sm font-medium text-neutral-300 dark:text-neutral-600"
                            >
                                <BanknotesIcon class="h-5 w-5" />{{
                                    t('cash.summary.balance')
                                }}
                            </div>
                            <p
                                class="mt-3 text-4xl font-semibold tracking-[-0.03em] tabular-nums"
                            >
                                {{ money(balanceMinor) }}
                            </p>
                        </div>
                        <p
                            class="text-sm font-semibold text-neutral-300 tabular-nums dark:text-neutral-600"
                        >
                            {{ currency }}
                        </p>
                    </div>
                </section>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('cash.summary.movements') }}
                        </h2>
                    </div>
                    <div
                        v-if="movements.data.length"
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <div
                            class="hidden grid-cols-[10rem_minmax(0,1fr)_12rem_10rem] gap-4 bg-neutral-50 px-7 py-3 text-xs font-semibold text-neutral-500 lg:grid dark:bg-neutral-950 dark:text-neutral-400"
                        >
                            <span>{{ t('cash.fields.date') }}</span>
                            <span>{{ t('cash.fields.type') }}</span>
                            <span>{{ t('cash.fields.guest') }}</span>
                            <span class="text-end">{{
                                t('cash.fields.amount')
                            }}</span>
                        </div>
                        <article
                            v-for="movement in movements.data"
                            :key="movement.id"
                            class="grid gap-4 px-5 py-5 sm:px-7 lg:grid-cols-[10rem_minmax(0,1fr)_12rem_10rem] lg:items-center"
                        >
                            <div>
                                <p
                                    class="text-xs font-medium text-neutral-500 lg:hidden"
                                >
                                    {{ t('cash.fields.date') }}
                                </p>
                                <p
                                    class="text-sm font-medium text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ movement.occurred_at }}
                                </p>
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t(`cash.types.${movement.type}`) }}
                                </p>
                                <p
                                    class="mt-1 text-sm break-words text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        movement.comment ??
                                        t('cash.summary.automatic')
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium text-neutral-500 lg:hidden"
                                >
                                    {{ t('cash.fields.guest') }}
                                </p>
                                <p
                                    v-if="movement.payment?.folio?.stay"
                                    class="text-sm text-neutral-700 dark:text-neutral-300"
                                >
                                    {{
                                        movement.payment.folio.stay
                                            .responsible_guest.first_name
                                    }}
                                    {{
                                        movement.payment.folio.stay
                                            .responsible_guest.last_name
                                    }}
                                </p>
                                <p
                                    v-else
                                    class="text-sm text-neutral-400 dark:text-neutral-600"
                                    aria-hidden="true"
                                >
                                    —
                                </p>
                            </div>
                            <div class="lg:text-end">
                                <p
                                    class="text-xs font-medium text-neutral-500 lg:hidden"
                                >
                                    {{ t('cash.fields.amount') }}
                                </p>
                                <p
                                    class="text-base font-semibold tabular-nums"
                                    :class="
                                        movement.direction === 'in'
                                            ? 'text-success-700 dark:text-success-400'
                                            : 'text-danger-700 dark:text-danger-400'
                                    "
                                >
                                    {{ movement.direction === 'in' ? '+' : '-'
                                    }}{{
                                        money(
                                            movement.amount_minor,
                                            movement.currency,
                                        )
                                    }}
                                </p>
                            </div>
                        </article>
                    </div>
                    <div v-else class="px-5 py-14 text-center sm:px-7">
                        <BanknotesIcon
                            class="mx-auto h-8 w-8 text-neutral-400"
                        />
                        <p
                            class="mt-4 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('cash.summary.empty') }}
                        </p>
                        <PrimaryButton type="button" class="mt-5" @click="open">
                            <PlusIcon class="h-4 w-4" />{{
                                t('cash.actions.record')
                            }}
                        </PrimaryButton>
                    </div>
                    <Pagination
                        v-if="movements.data.length"
                        :pagination="movements"
                    />
                </section>
            </div>
        </div>

        <Modal
            :show="isOpen"
            max-width="lg"
            :closeable="!form.processing"
            aria-labelledby="cash-movement-title"
            @close="close"
        >
            <form class="grid gap-5 p-6 sm:p-7" @submit.prevent="submit">
                <div>
                    <h2
                        id="cash-movement-title"
                        class="text-xl font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('cash.actions.record') }}
                    </h2>
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="cash-type"
                        :value="t('cash.fields.type')"
                        required
                    /><select
                        id="cash-type"
                        v-model="form.type"
                        autofocus
                        aria-describedby="cash-type-error"
                        :aria-invalid="Boolean(form.errors.type)"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                        :class="
                            form.errors.type
                                ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                                : 'border-neutral-300 dark:border-neutral-700'
                        "
                    >
                        <option value="manual_entry">
                            {{ t('cash.types.manual_entry') }}
                        </option>
                        <option value="withdrawal">
                            {{ t('cash.types.withdrawal') }}
                        </option></select
                    ><InputError
                        id="cash-type-error"
                        :message="form.errors.type"
                    />
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="cash-amount"
                        :value="t('cash.fields.amount')"
                        required
                    />
                    <div class="relative">
                        <TextInput
                            id="cash-amount"
                            v-model="form.amount"
                            type="number"
                            min="0.01"
                            step="0.01"
                            inputmode="decimal"
                            class="w-full pe-16 tabular-nums"
                            required
                            aria-describedby="cash-amount-error"
                            :aria-invalid="Boolean(form.errors.amount)"
                            :invalid="Boolean(form.errors.amount)"
                        /><span
                            class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs font-semibold text-neutral-500"
                            >{{ currency }}</span
                        >
                    </div>
                    <InputError
                        id="cash-amount-error"
                        :message="form.errors.amount"
                    />
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="cash-comment"
                        :value="t('cash.fields.comment')"
                        required
                    /><textarea
                        id="cash-comment"
                        v-model="form.comment"
                        rows="3"
                        maxlength="255"
                        required
                        aria-describedby="cash-comment-error"
                        :aria-invalid="Boolean(form.errors.comment)"
                        class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                        :class="
                            form.errors.comment
                                ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                                : 'border-neutral-300 dark:border-neutral-700'
                        "
                    /><InputError
                        id="cash-comment-error"
                        :message="form.errors.comment"
                    />
                </div>
                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        type="button"
                        :disabled="form.processing"
                        @click="close"
                        >{{ t('app.cancel') }}</SecondaryButton
                    ><PrimaryButton type="submit" :disabled="form.processing"
                        ><ArrowDownTrayIcon
                            v-if="form.type === 'manual_entry'"
                            class="h-4 w-4"
                        /><ArrowUpTrayIcon v-else class="h-4 w-4" />{{
                            t('cash.actions.record')
                        }}</PrimaryButton
                    >
                </div>
            </form>
        </Modal>
    </DefaultLayout>
</template>
