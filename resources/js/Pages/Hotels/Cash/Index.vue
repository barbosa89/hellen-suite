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
    ArrowRightIcon,
    ArrowUpTrayIcon,
    BanknotesIcon,
    CheckCircleIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    LockClosedIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    movements: Object,
    balanceMinor: Number,
    currency: String,
    activeShift: Object,
    shiftSummary: Array,
    recentShifts: Array,
    unassignedMovementCount: Number,
    flash: { type: Object, default: () => ({}) },
});
const { t, locale } = useI18n();
const modal = ref(null);
const movementForm = useForm({ type: 'manual_entry', amount: '', comment: '' });
const openForm = useForm({ opening_amount: '0.00', opening_note: '' });
const reconcileForm = useForm({
    reconciliations: [],
    closing_note: '',
    opening_note: '',
});

const cashRow = computed(() =>
    props.shiftSummary.find(
        (row) => row.method === 'cash' && row.currency === props.currency,
    ),
);

function money(minor, currency = props.currency) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(Number(minor) / 100);
}

function dateTime(value) {
    return new Intl.DateTimeFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

function hasDifference(shift) {
    return (shift.reconciliations ?? []).some(
        (row) => Number(row.difference_minor) !== 0,
    );
}

function closeModal() {
    if (
        !movementForm.processing &&
        !openForm.processing &&
        !reconcileForm.processing
    ) {
        modal.value = null;
        movementForm.clearErrors();
        openForm.clearErrors();
        reconcileForm.clearErrors();
    }
}

function openMovement() {
    movementForm.reset();
    modal.value = 'movement';
}

function openShift() {
    openForm.reset();
    modal.value = 'open';
}

function openReconciliation(type) {
    reconcileForm.reconciliations = props.shiftSummary.map((row) => ({
        method: row.method,
        currency: row.currency,
        declared_amount: (row.expected_closing_minor / 100).toFixed(2),
    }));
    reconcileForm.closing_note = '';
    reconcileForm.opening_note = '';
    reconcileForm.clearErrors();
    modal.value = type;
}

function declaredMinor(index) {
    return Math.round(
        Number(reconcileForm.reconciliations[index]?.declared_amount || 0) *
            100,
    );
}

function difference(index) {
    return (
        declaredMinor(index) - props.shiftSummary[index].expected_closing_minor
    );
}

function submitMovement() {
    movementForm.post(route('hotels.cash.store', props.hotel.id), {
        preserveScroll: true,
        onSuccess: () => {
            modal.value = null;
            movementForm.reset();
        },
    });
}

function submitOpen() {
    openForm.post(route('hotels.cash.shifts.store', props.hotel.id), {
        onSuccess: () => {
            modal.value = null;
            openForm.reset();
        },
    });
}

function submitReconciliation() {
    const routeName =
        modal.value === 'handover'
            ? 'hotels.cash.shifts.handover'
            : 'hotels.cash.shifts.close';
    reconcileForm.post(
        route(routeName, [props.hotel.id, props.activeShift.id]),
        {
            onSuccess: () => {
                modal.value = null;
                reconcileForm.reset();
            },
        },
    );
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
                    <template #actions>
                        <div v-if="activeShift" class="flex flex-wrap gap-2">
                            <SecondaryButton
                                type="button"
                                @click="openReconciliation('close')"
                            >
                                <LockClosedIcon class="h-4 w-4" />{{
                                    t('cash.actions.close_shift')
                                }}
                            </SecondaryButton>
                            <PrimaryButton
                                type="button"
                                @click="openReconciliation('handover')"
                            >
                                <ArrowRightIcon class="h-4 w-4" />{{
                                    t('cash.actions.handover')
                                }}
                            </PrimaryButton>
                        </div>
                        <PrimaryButton v-else type="button" @click="openShift">
                            <PlusIcon class="h-4 w-4" />{{
                                t('cash.actions.open_shift')
                            }}
                        </PrimaryButton>
                    </template>
                </PageHeader>

                <FlashMessage v-if="flash.success" :message="flash.success" />

                <section
                    v-if="activeShift"
                    class="rounded-2xl bg-neutral-900 text-white shadow-sm dark:bg-white dark:text-neutral-950"
                >
                    <div
                        class="grid gap-8 px-5 py-6 sm:px-7 sm:py-8 lg:grid-cols-[1.4fr_1fr] lg:items-end"
                    >
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span
                                    class="bg-success-500/15 text-success-300 dark:text-success-700 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                >
                                    <span
                                        class="bg-success-400 h-2 w-2 rounded-full"
                                        aria-hidden="true"
                                    ></span
                                    >{{ t('cash.status.open') }}
                                </span>
                                <span
                                    class="text-sm font-medium text-neutral-400 dark:text-neutral-600"
                                    >{{
                                        t('cash.shift_number', {
                                            number: activeShift.number,
                                        })
                                    }}</span
                                >
                            </div>
                            <p
                                class="mt-7 text-sm font-medium text-neutral-400 dark:text-neutral-600"
                            >
                                {{ t('cash.summary.expected_cash') }}
                            </p>
                            <p
                                class="mt-2 text-4xl font-semibold tracking-[-0.04em] tabular-nums sm:text-5xl"
                            >
                                {{ money(balanceMinor) }}
                            </p>
                            <p
                                class="mt-4 flex items-center gap-2 text-sm text-neutral-400 dark:text-neutral-600"
                            >
                                <ClockIcon class="h-4 w-4" />{{
                                    t('cash.summary.opened_at', {
                                        date: dateTime(activeShift.opened_at),
                                    })
                                }}
                            </p>
                        </div>
                        <dl
                            class="grid gap-px overflow-hidden rounded-2xl bg-white/10 sm:grid-cols-3 dark:bg-neutral-950/10"
                        >
                            <div
                                class="bg-white/5 px-3 py-4 dark:bg-neutral-950/5"
                            >
                                <dt
                                    class="text-xs text-neutral-400 dark:text-neutral-600"
                                >
                                    {{ t('cash.summary.opening') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm font-semibold tabular-nums"
                                >
                                    {{ money(cashRow?.opening_minor ?? 0) }}
                                </dd>
                            </div>
                            <div
                                class="bg-white/5 px-3 py-4 dark:bg-neutral-950/5"
                            >
                                <dt
                                    class="text-xs text-neutral-400 dark:text-neutral-600"
                                >
                                    {{ t('cash.summary.inflows') }}
                                </dt>
                                <dd
                                    class="text-success-300 dark:text-success-700 mt-2 text-sm font-semibold tabular-nums"
                                >
                                    +{{ money(cashRow?.inflow_minor ?? 0) }}
                                </dd>
                            </div>
                            <div
                                class="bg-white/5 px-3 py-4 dark:bg-neutral-950/5"
                            >
                                <dt
                                    class="text-xs text-neutral-400 dark:text-neutral-600"
                                >
                                    {{ t('cash.summary.outflows') }}
                                </dt>
                                <dd
                                    class="text-danger-300 dark:text-danger-700 mt-2 text-sm font-semibold tabular-nums"
                                >
                                    -{{ money(cashRow?.outflow_minor ?? 0) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <section
                    v-else
                    class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div
                        class="grid gap-8 px-5 py-10 sm:px-8 lg:grid-cols-[1fr_auto] lg:items-center lg:py-12"
                    >
                        <div class="max-w-2xl">
                            <div
                                class="bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300 flex h-12 w-12 items-center justify-center rounded-2xl"
                            >
                                <BanknotesIcon class="h-6 w-6" />
                            </div>
                            <h2
                                class="mt-5 text-2xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                            >
                                {{ t('cash.empty.title') }}
                            </h2>
                            <p
                                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('cash.empty.description') }}
                            </p>
                        </div>
                        <PrimaryButton type="button" @click="openShift"
                            ><PlusIcon class="h-4 w-4" />{{
                                t('cash.actions.open_shift')
                            }}</PrimaryButton
                        >
                    </div>
                </section>

                <div
                    v-if="activeShift"
                    class="grid gap-7 xl:grid-cols-[minmax(0,1.55fr)_minmax(20rem,0.7fr)]"
                >
                    <section
                        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    >
                        <div
                            class="flex items-center justify-between gap-4 border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                        >
                            <div>
                                <h2
                                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('cash.summary.movements') }}
                                </h2>
                                <p
                                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                >
                                    {{ t('cash.summary.current_shift_only') }}
                                </p>
                            </div>
                            <PrimaryButton type="button" @click="openMovement"
                                ><PlusIcon class="h-4 w-4" />{{
                                    t('cash.actions.record')
                                }}</PrimaryButton
                            >
                        </div>
                        <div
                            v-if="movements.data.length"
                            class="divide-y divide-neutral-200 dark:divide-neutral-800"
                        >
                            <article
                                v-for="movement in movements.data"
                                :key="movement.id"
                                class="grid gap-3 px-5 py-5 sm:grid-cols-[1fr_auto] sm:items-center sm:px-7"
                            >
                                <div class="min-w-0">
                                    <div
                                        class="flex flex-wrap items-center gap-x-3 gap-y-1"
                                    >
                                        <p
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{
                                                t(`cash.types.${movement.type}`)
                                            }}
                                        </p>
                                        <time
                                            class="text-xs text-neutral-500 tabular-nums dark:text-neutral-400"
                                            >{{
                                                dateTime(movement.occurred_at)
                                            }}</time
                                        >
                                    </div>
                                    <p
                                        class="mt-1 truncate text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{
                                            movement.comment ??
                                            t('cash.summary.automatic')
                                        }}
                                    </p>
                                </div>
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
                            </article>
                        </div>
                        <div v-else class="px-5 py-12 text-center sm:px-7">
                            <BanknotesIcon
                                class="mx-auto h-8 w-8 text-neutral-400"
                            />
                            <p
                                class="mt-4 text-sm text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('cash.summary.empty_shift') }}
                            </p>
                        </div>
                        <Pagination
                            v-if="movements.data.length"
                            :pagination="movements"
                        />
                    </section>

                    <section
                        class="rounded-2xl bg-white p-5 shadow-sm sm:p-7 dark:bg-neutral-900"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('cash.reconciliation.title') }}
                        </h2>
                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            {{ t('cash.reconciliation.preview') }}
                        </p>
                        <div class="mt-5 grid gap-3">
                            <div
                                v-for="row in shiftSummary"
                                :key="`${row.method}-${row.currency}`"
                                class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-800"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <span
                                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                                        >{{
                                            t(`payments.methods.${row.method}`)
                                        }}</span
                                    >
                                    <span
                                        class="text-xs font-semibold text-neutral-500 dark:text-neutral-400"
                                        >{{ row.currency }}</span
                                    >
                                </div>
                                <p
                                    class="mt-3 text-xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{
                                        money(
                                            row.expected_closing_minor,
                                            row.currency,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('cash.history.title') }}
                        </h2>
                    </div>
                    <div
                        v-if="recentShifts.length"
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <Link
                            v-for="shift in recentShifts"
                            :key="shift.id"
                            :href="
                                route('hotels.cash.shifts.show', [
                                    hotel.id,
                                    shift.id,
                                ])
                            "
                            class="focus-visible:ring-primary-500 group flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-neutral-50 focus-visible:ring-2 focus-visible:outline-hidden focus-visible:ring-inset sm:px-7 dark:hover:bg-neutral-800/60"
                        >
                            <div>
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{
                                        t('cash.shift_number', {
                                            number: shift.number,
                                        })
                                    }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                >
                                    {{ dateTime(shift.opened_at) }} ·
                                    {{ dateTime(shift.closed_at) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <ExclamationTriangleIcon
                                    v-if="hasDifference(shift)"
                                    class="text-secondary-800 dark:text-secondary-300 h-5 w-5"
                                />
                                <CheckCircleIcon
                                    v-else
                                    class="text-success-600 h-5 w-5"
                                />
                                <ArrowRightIcon
                                    class="h-4 w-4 text-neutral-400 transition group-hover:translate-x-0.5"
                                />
                            </div>
                        </Link>
                    </div>
                    <p
                        v-else
                        class="px-5 py-10 text-center text-sm text-neutral-500 sm:px-7 dark:text-neutral-400"
                    >
                        {{ t('cash.history.empty') }}
                    </p>
                    <div
                        v-if="unassignedMovementCount"
                        class="border-secondary-200 bg-secondary-50 text-secondary-800 dark:border-secondary-900/50 dark:bg-secondary-900/30 dark:text-secondary-300 flex gap-3 border-t px-5 py-4 text-sm sm:px-7"
                    >
                        <ExclamationTriangleIcon
                            class="mt-0.5 h-5 w-5 shrink-0"
                        />
                        <p>
                            {{
                                t('cash.history.unassigned', {
                                    count: unassignedMovementCount,
                                })
                            }}
                        </p>
                    </div>
                </section>
            </div>
        </div>

        <Modal
            :show="modal === 'open'"
            max-width="lg"
            :closeable="!openForm.processing"
            aria-labelledby="open-shift-title"
            @close="closeModal"
        >
            <form class="grid gap-5 p-6 sm:p-7" @submit.prevent="submitOpen">
                <div>
                    <h2
                        id="open-shift-title"
                        class="text-xl font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('cash.open.title') }}
                    </h2>
                    <p
                        class="mt-2 text-sm text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('cash.open.description') }}
                    </p>
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="opening-amount"
                        :value="t('cash.fields.opening_amount')"
                        required
                    />
                    <div class="relative">
                        <TextInput
                            id="opening-amount"
                            v-model="openForm.opening_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            inputmode="decimal"
                            class="w-full pe-16 tabular-nums"
                            required
                            autofocus
                            :invalid="Boolean(openForm.errors.opening_amount)"
                        /><span
                            class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs font-semibold text-neutral-500"
                            >{{ currency }}</span
                        >
                    </div>
                    <InputError :message="openForm.errors.opening_amount" />
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="opening-note"
                        :value="t('cash.fields.opening_note')"
                    /><textarea
                        id="opening-note"
                        v-model="openForm.opening_note"
                        rows="3"
                        maxlength="255"
                        class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    ></textarea
                    ><InputError :message="openForm.errors.opening_note" />
                </div>
                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        type="button"
                        :disabled="openForm.processing"
                        @click="closeModal"
                        >{{ t('app.cancel') }}</SecondaryButton
                    ><PrimaryButton
                        type="submit"
                        :disabled="openForm.processing"
                        ><PlusIcon class="h-4 w-4" />{{
                            t('cash.actions.open_shift')
                        }}</PrimaryButton
                    >
                </div>
            </form>
        </Modal>

        <Modal
            :show="modal === 'movement'"
            max-width="lg"
            :closeable="!movementForm.processing"
            aria-labelledby="cash-movement-title"
            @close="closeModal"
        >
            <form
                class="grid gap-5 p-6 sm:p-7"
                @submit.prevent="submitMovement"
            >
                <h2
                    id="cash-movement-title"
                    class="text-xl font-semibold text-neutral-950 dark:text-white"
                >
                    {{ t('cash.actions.record') }}
                </h2>
                <div class="grid gap-2">
                    <InputLabel
                        for="cash-type"
                        :value="t('cash.fields.type')"
                        required
                    /><select
                        id="cash-type"
                        v-model="movementForm.type"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    >
                        <option value="manual_entry">
                            {{ t('cash.types.manual_entry') }}
                        </option>
                        <option value="withdrawal">
                            {{ t('cash.types.withdrawal') }}
                        </option></select
                    ><InputError :message="movementForm.errors.type" />
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
                            v-model="movementForm.amount"
                            type="number"
                            min="0.01"
                            step="0.01"
                            inputmode="decimal"
                            class="w-full pe-16 tabular-nums"
                            required
                            :invalid="Boolean(movementForm.errors.amount)"
                        /><span
                            class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs font-semibold text-neutral-500"
                            >{{ currency }}</span
                        >
                    </div>
                    <InputError :message="movementForm.errors.amount" />
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="cash-comment"
                        :value="t('cash.fields.comment')"
                        required
                    /><textarea
                        id="cash-comment"
                        v-model="movementForm.comment"
                        rows="3"
                        maxlength="255"
                        required
                        class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    ></textarea
                    ><InputError :message="movementForm.errors.comment" />
                </div>
                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        type="button"
                        :disabled="movementForm.processing"
                        @click="closeModal"
                        >{{ t('app.cancel') }}</SecondaryButton
                    ><PrimaryButton
                        type="submit"
                        :disabled="movementForm.processing"
                        ><ArrowDownTrayIcon
                            v-if="movementForm.type === 'manual_entry'"
                            class="h-4 w-4"
                        /><ArrowUpTrayIcon v-else class="h-4 w-4" />{{
                            t('cash.actions.record')
                        }}</PrimaryButton
                    >
                </div>
            </form>
        </Modal>

        <Modal
            :show="modal === 'close' || modal === 'handover'"
            max-width="2xl"
            :closeable="!reconcileForm.processing"
            aria-labelledby="reconciliation-title"
            @close="closeModal"
        >
            <form
                class="grid gap-6 p-6 sm:p-7"
                @submit.prevent="submitReconciliation"
            >
                <div>
                    <h2
                        id="reconciliation-title"
                        class="text-xl font-semibold text-neutral-950 dark:text-white"
                    >
                        {{
                            modal === 'handover'
                                ? t('cash.handover.title')
                                : t('cash.close.title')
                        }}
                    </h2>
                    <p
                        class="mt-2 text-sm text-neutral-600 dark:text-neutral-400"
                    >
                        {{
                            modal === 'handover'
                                ? t('cash.handover.description')
                                : t('cash.close.description')
                        }}
                    </p>
                </div>
                <div
                    class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800"
                >
                    <div
                        v-for="(row, index) in shiftSummary"
                        :key="`${row.method}-${row.currency}`"
                        class="grid gap-4 border-b border-neutral-200 p-4 last:border-b-0 sm:grid-cols-[1fr_1fr_auto] sm:items-end dark:border-neutral-800"
                    >
                        <div>
                            <p
                                class="text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t(`payments.methods.${row.method}`) }} ·
                                {{ row.currency }}
                            </p>
                            <p
                                class="mt-1 text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                {{ t('cash.reconciliation.expected') }}:
                                <strong class="tabular-nums">{{
                                    money(
                                        row.expected_closing_minor,
                                        row.currency,
                                    )
                                }}</strong>
                            </p>
                        </div>
                        <div class="grid gap-1.5">
                            <InputLabel
                                :for="`declared-${index}`"
                                :value="t('cash.reconciliation.declared')"
                                required
                            /><TextInput
                                :id="`declared-${index}`"
                                v-model="
                                    reconcileForm.reconciliations[index]
                                        .declared_amount
                                "
                                type="number"
                                :min="row.method === 'cash' ? 0 : undefined"
                                step="0.01"
                                inputmode="decimal"
                                class="w-full tabular-nums"
                                required
                                :invalid="
                                    Boolean(
                                        reconcileForm.errors[
                                            `reconciliations.${index}.declared_amount`
                                        ],
                                    )
                                "
                            />
                            <InputError
                                :message="
                                    reconcileForm.errors[
                                        `reconciliations.${index}.declared_amount`
                                    ]
                                "
                            />
                        </div>
                        <div class="sm:min-w-28 sm:text-end">
                            <p
                                class="text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                {{ t('cash.reconciliation.difference') }}
                            </p>
                            <p
                                class="mt-1 font-semibold tabular-nums"
                                :class="
                                    difference(index) === 0
                                        ? 'text-success-700 dark:text-success-400'
                                        : difference(index) < 0
                                          ? 'text-danger-700 dark:text-danger-400'
                                          : 'text-secondary-800 dark:text-secondary-300'
                                "
                            >
                                {{ money(difference(index), row.currency) }}
                            </p>
                        </div>
                    </div>
                </div>
                <InputError
                    :message="
                        reconcileForm.errors.reconciliations ||
                        reconcileForm.errors.cash_shift
                    "
                />
                <div class="grid gap-2">
                    <InputLabel
                        for="closing-note"
                        :value="t('cash.fields.closing_note')"
                    /><textarea
                        id="closing-note"
                        v-model="reconcileForm.closing_note"
                        rows="2"
                        maxlength="255"
                        class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    ></textarea>
                    <InputError :message="reconcileForm.errors.closing_note" />
                </div>
                <div v-if="modal === 'handover'" class="grid gap-2">
                    <InputLabel
                        for="next-note"
                        :value="t('cash.fields.next_opening_note')"
                    /><textarea
                        id="next-note"
                        v-model="reconcileForm.opening_note"
                        rows="2"
                        maxlength="255"
                        class="focus:border-primary-500 focus:ring-primary-500/20 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    ></textarea>
                    <InputError :message="reconcileForm.errors.opening_note" />
                </div>
                <div
                    class="bg-secondary-50 text-secondary-800 dark:bg-secondary-900/30 dark:text-secondary-300 rounded-xl p-4 text-sm"
                >
                    <strong>{{
                        t('cash.reconciliation.irreversible_title')
                    }}</strong>
                    {{ t('cash.reconciliation.irreversible') }}
                </div>
                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        type="button"
                        :disabled="reconcileForm.processing"
                        @click="closeModal"
                        >{{ t('app.cancel') }}</SecondaryButton
                    ><PrimaryButton
                        type="submit"
                        :disabled="reconcileForm.processing"
                        ><ArrowRightIcon
                            v-if="modal === 'handover'"
                            class="h-4 w-4"
                        /><LockClosedIcon v-else class="h-4 w-4" />{{
                            modal === 'handover'
                                ? t('cash.actions.confirm_handover')
                                : t('cash.actions.confirm_close')
                        }}</PrimaryButton
                    >
                </div>
            </form>
        </Modal>
    </DefaultLayout>
</template>
