<script setup>
import {
    ArrowUturnLeftIcon,
    BanknotesIcon,
    PaperClipIcon,
    PrinterIcon,
} from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

defineProps({ payments: Array });

const { t, locale } = useI18n();

function money(payment) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: payment.currency,
        minimumFractionDigits: 2,
    }).format(Number(payment.amount_minor) / 100);
}

function dateTime(value) {
    return new Intl.DateTimeFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value.replace(' ', 'T')));
}
</script>

<template>
    <div class="mt-5 border-t border-neutral-200 pt-4 dark:border-neutral-800">
        <h4 class="text-sm font-semibold text-neutral-950 dark:text-white">
            {{ t('payments.summary.history') }}
        </h4>

        <ul
            v-if="payments.length"
            class="mt-3 divide-y divide-neutral-200 border-y border-neutral-200 dark:divide-neutral-800 dark:border-neutral-800"
        >
            <li
                v-for="payment in payments"
                :key="payment.id"
                class="grid gap-3 py-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center"
            >
                <div class="flex min-w-0 items-start gap-3">
                    <span
                        class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                        :class="
                            payment.type === 'receipt'
                                ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                                : 'bg-secondary-50 text-secondary-800 dark:bg-secondary-950 dark:text-secondary-300'
                        "
                    >
                        <BanknotesIcon
                            v-if="payment.type === 'receipt'"
                            class="h-4.5 w-4.5"
                        />
                        <ArrowUturnLeftIcon v-else class="h-4.5 w-4.5" />
                    </span>
                    <div class="min-w-0">
                        <div
                            class="flex flex-wrap items-baseline gap-x-2 gap-y-1"
                        >
                            <p
                                class="text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{
                                    payment.type === 'receipt'
                                        ? t('payments.types.receipt')
                                        : t('payments.types.refund')
                                }}
                            </p>
                            <span
                                class="text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                {{ t(`payments.methods.${payment.method}`) }} ·
                                {{ dateTime(payment.paid_at) }}
                            </span>
                        </div>
                        <p
                            v-if="payment.comment"
                            class="mt-1 text-sm break-words text-neutral-600 dark:text-neutral-400"
                        >
                            {{ payment.comment }}
                        </p>
                        <p
                            v-if="
                                payment.type === 'receipt' &&
                                !payment.voucher_a4_url
                            "
                            class="mt-1 text-xs font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            {{ t('payments.voucher.available_after_checkout') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                    <p
                        class="mr-1 text-sm font-semibold tabular-nums"
                        :class="
                            payment.type === 'receipt'
                                ? 'text-success-700 dark:text-success-400'
                                : 'text-secondary-800 dark:text-secondary-300'
                        "
                    >
                        {{ payment.type === 'refund' ? '−' : '+'
                        }}{{ money(payment) }}
                    </p>
                    <a
                        v-if="payment.support_url"
                        :href="payment.support_url"
                        target="_blank"
                        rel="noopener"
                        class="focus-visible:ring-primary-500 inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-neutral-300 px-3 py-2 text-xs font-semibold text-neutral-700 transition hover:bg-neutral-50 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus-visible:ring-offset-neutral-900"
                    >
                        <PaperClipIcon class="h-4 w-4" />
                        {{ t('payments.actions.view_support') }}
                    </a>
                    <a
                        v-if="payment.voucher_a4_url"
                        :href="payment.voucher_a4_url"
                        target="_blank"
                        rel="noopener"
                        class="focus-visible:ring-primary-500 inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-neutral-300 px-3 py-2 text-xs font-semibold text-neutral-700 transition hover:bg-neutral-50 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus-visible:ring-offset-neutral-900"
                    >
                        <PrinterIcon class="h-4 w-4" />
                        {{ t('payments.actions.print_a4') }}
                    </a>
                    <a
                        v-if="payment.voucher_thermal_url"
                        :href="payment.voucher_thermal_url"
                        target="_blank"
                        rel="noopener"
                        class="bg-primary-700 hover:bg-primary-800 focus-visible:ring-primary-500 dark:bg-primary-400 dark:hover:bg-primary-300 inline-flex min-h-9 items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-white transition focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none dark:text-neutral-950 dark:focus-visible:ring-offset-neutral-900"
                    >
                        <PrinterIcon class="h-4 w-4" />
                        {{ t('payments.actions.print_thermal') }}
                    </a>
                </div>
            </li>
        </ul>

        <p v-else class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
            {{ t('payments.summary.empty') }}
        </p>
    </div>
</template>
