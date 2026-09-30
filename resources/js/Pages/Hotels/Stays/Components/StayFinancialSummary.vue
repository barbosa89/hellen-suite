<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {
    BanknotesIcon,
    CheckCircleIcon,
    PlusIcon,
    ReceiptPercentIcon,
} from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

defineProps({ summary: Object, currency: String, active: Boolean });
const emit = defineEmits(['payment', 'charge', 'adjustment']);
const { t, locale } = useI18n();

function money(minor, currency) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(Number(minor) / 100);
}

function additionalCharges(folio) {
    return folio.charges.filter((charge) => charge.type === 'manual');
}

function date(dateTime) {
    return new Intl.DateTimeFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        dateStyle: 'medium',
    }).format(new Date(dateTime.replace(' ', 'T')));
}
</script>

<template>
    <section
        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
    >
        <div
            class="flex flex-col gap-4 border-b border-neutral-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7 dark:border-neutral-800"
        >
            <div>
                <h2
                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                >
                    {{ t('payments.title') }}
                </h2>
                <p
                    class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{ t('payments.summary.description') }}
                </p>
            </div>
            <div class="text-start sm:text-end">
                <p
                    class="text-xs font-semibold text-neutral-500 dark:text-neutral-400"
                >
                    {{ t('payments.summary.balance') }}
                </p>
                <p
                    class="mt-1 text-2xl font-semibold tabular-nums"
                    :class="{
                        'text-success-700 dark:text-success-400':
                            summary.balance_minor === 0,
                        'text-neutral-950 dark:text-white':
                            summary.balance_minor > 0,
                        'text-danger-700 dark:text-danger-400':
                            summary.balance_minor < 0,
                    }"
                    aria-live="polite"
                >
                    {{ money(summary.balance_minor, currency) }}
                </p>
            </div>
        </div>

        <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
            <article
                v-for="folio in summary.folios"
                :key="folio.id"
                class="px-5 py-5 sm:px-7"
            >
                <div
                    class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between"
                >
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3
                                class="font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ folio.label }}
                            </h3>
                            <span
                                v-if="folio.balance_minor === 0"
                                class="bg-success-50 text-success-800 dark:bg-success-950 dark:text-success-300 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                            >
                                <CheckCircleIcon class="h-4 w-4" />{{
                                    t('payments.summary.settled')
                                }}
                            </span>
                            <span
                                v-if="folio.projected_charge_minor > 0"
                                class="bg-secondary-50 text-secondary-800 dark:bg-secondary-950 dark:text-secondary-300 rounded-full px-2.5 py-1 text-xs font-semibold"
                                >{{ t('payments.summary.projected') }}</span
                            >
                        </div>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{
                                folio.rooms
                                    .map((room) => room.number)
                                    .join(' → ')
                            }}
                        </p>
                    </div>

                    <dl
                        class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-4 xl:min-w-[34rem]"
                    >
                        <div>
                            <dt class="text-xs text-neutral-500">
                                {{ t('payments.summary.charges') }}
                            </dt>
                            <dd
                                class="mt-1 font-semibold tabular-nums"
                                :class="
                                    folio.balance_minor === 0
                                        ? 'text-success-700 dark:text-success-400'
                                        : 'text-neutral-950 dark:text-white'
                                "
                            >
                                {{
                                    money(
                                        folio.posted_charge_minor +
                                            folio.projected_charge_minor,
                                        folio.currency,
                                    )
                                }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-500">
                                {{ t('payments.summary.adjustments') }}
                            </dt>
                            <dd
                                class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                            >
                                {{
                                    money(
                                        folio.adjustment_minor,
                                        folio.currency,
                                    )
                                }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-500">
                                {{ t('payments.summary.paid') }}
                            </dt>
                            <dd
                                class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                            >
                                {{ money(folio.paid_minor, folio.currency) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-500">
                                {{ t('payments.summary.balance') }}
                            </dt>
                            <dd
                                class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                            >
                                {{ money(folio.balance_minor, folio.currency) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div
                    class="mt-5 border-t border-neutral-200 pt-4 dark:border-neutral-800"
                >
                    <h4
                        class="text-sm font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('payments.summary.additional_charges') }}
                    </h4>
                    <ul
                        v-if="additionalCharges(folio).length"
                        class="mt-3 divide-y divide-neutral-200 border-y border-neutral-200 dark:divide-neutral-800 dark:border-neutral-800"
                    >
                        <li
                            v-for="charge in additionalCharges(folio)"
                            :key="charge.id"
                            class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-3"
                        >
                            <div class="min-w-0">
                                <p
                                    class="text-sm font-medium break-words text-neutral-950 dark:text-white"
                                >
                                    {{ charge.description }}
                                </p>
                                <p
                                    class="mt-0.5 text-xs text-neutral-500 tabular-nums dark:text-neutral-400"
                                >
                                    {{ date(charge.posted_at) }}
                                </p>
                            </div>
                            <p
                                class="text-sm font-semibold text-neutral-950 tabular-nums dark:text-white"
                            >
                                {{
                                    money(
                                        charge.total_amount_minor,
                                        folio.currency,
                                    )
                                }}
                            </p>
                        </li>
                    </ul>
                    <p
                        v-else
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ t('payments.summary.no_additional_charges') }}
                    </p>
                </div>

                <div
                    v-if="active && !folio.closed_at"
                    class="mt-5 flex flex-wrap gap-2"
                >
                    <PrimaryButton
                        v-if="folio.balance_minor > 0"
                        type="button"
                        @click="emit('payment', folio)"
                        ><BanknotesIcon class="h-4 w-4" />{{
                            t('payments.actions.record_payment')
                        }}</PrimaryButton
                    >
                    <SecondaryButton
                        type="button"
                        @click="emit('charge', folio)"
                        ><PlusIcon class="h-4 w-4" />{{
                            t('payments.actions.add_charge')
                        }}</SecondaryButton
                    >
                    <SecondaryButton
                        v-if="folio.balance_minor > 0"
                        type="button"
                        @click="emit('adjustment', folio)"
                        ><ReceiptPercentIcon class="h-4 w-4" />{{
                            t('payments.actions.apply_adjustment')
                        }}</SecondaryButton
                    >
                </div>
            </article>
        </div>
    </section>
</template>
