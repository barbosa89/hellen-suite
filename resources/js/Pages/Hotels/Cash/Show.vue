<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { ArrowLeftIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    hotel: Object,
    shift: Object,
    summary: Array,
    movements: Object,
});
const { t, locale } = useI18n();

function money(minor, currency) {
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
</script>

<template>
    <Head :title="t('cash.shift_number', { number: shift.number })" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('cash.shift_number', { number: shift.number })"
                    :description="`${dateTime(shift.opened_at)} → ${dateTime(shift.closed_at)}`"
                >
                    <template #actions>
                        <div class="flex flex-wrap gap-2">
                            <ActionLink
                                :href="route('hotels.cash.index', hotel.id)"
                                variant="secondary"
                                ><ArrowLeftIcon class="h-4 w-4" />{{
                                    t('cash.actions.back')
                                }}</ActionLink
                            >
                            <a
                                :href="
                                    route('hotels.cash.shifts.report', [
                                        hotel.id,
                                        shift.id,
                                        'a4',
                                    ])
                                "
                                target="_blank"
                                rel="noopener"
                                class="focus-visible:ring-primary-500 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 shadow-sm transition-[background-color,border-color,color,box-shadow] duration-150 ease-out hover:border-neutral-400 hover:bg-neutral-50 hover:text-neutral-950 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden motion-reduce:transition-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:border-neutral-600 dark:hover:bg-neutral-800 dark:hover:text-white dark:focus-visible:ring-offset-neutral-950"
                                ><DocumentTextIcon class="h-4 w-4" />{{
                                    t('cash.actions.print_a4')
                                }}</a
                            >
                            <a
                                :href="
                                    route('hotels.cash.shifts.report', [
                                        hotel.id,
                                        shift.id,
                                        'thermal',
                                    ])
                                "
                                target="_blank"
                                rel="noopener"
                                class="focus-visible:ring-primary-500 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-neutral-950 bg-neutral-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-[background-color,border-color,color,box-shadow] duration-150 ease-out hover:border-neutral-800 hover:bg-neutral-800 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden motion-reduce:transition-none dark:border-white dark:bg-white dark:text-neutral-950 dark:hover:border-neutral-200 dark:hover:bg-neutral-200 dark:focus-visible:ring-offset-neutral-950"
                                ><DocumentTextIcon class="h-4 w-4" />{{
                                    t('cash.actions.print_thermal')
                                }}</a
                            >
                        </div>
                    </template>
                </PageHeader>

                <section
                    class="overflow-hidden rounded-2xl bg-neutral-900 px-5 py-6 text-white shadow-sm sm:px-7 sm:py-8 dark:bg-white dark:text-neutral-950"
                >
                    <div
                        class="flex flex-wrap items-center justify-between gap-4"
                    >
                        <div>
                            <span
                                class="bg-success-500/15 text-success-300 dark:text-success-700 inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                >{{ t('cash.status.closed') }}</span
                            >
                            <p
                                class="mt-5 text-sm text-neutral-400 dark:text-neutral-600"
                            >
                                {{ t('cash.reconciliation.title') }}
                            </p>
                        </div>
                        <p
                            class="text-sm font-semibold text-neutral-400 dark:text-neutral-600"
                        >
                            TR-{{ String(shift.number).padStart(6, '0') }}
                        </p>
                    </div>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <article
                            v-for="row in summary"
                            :key="`${row.method}-${row.currency}`"
                            class="rounded-2xl bg-white/10 p-5 dark:bg-neutral-950/10"
                        >
                            <div class="flex justify-between gap-3">
                                <h2 class="font-semibold">
                                    {{ t(`payments.methods.${row.method}`) }}
                                </h2>
                                <span
                                    class="text-xs font-semibold text-neutral-400 dark:text-neutral-600"
                                    >{{ row.currency }}</span
                                >
                            </div>
                            <p
                                class="mt-5 text-xs text-neutral-400 dark:text-neutral-600"
                            >
                                {{ t('cash.reconciliation.declared') }}
                            </p>
                            <p class="mt-1 text-2xl font-semibold tabular-nums">
                                {{
                                    money(
                                        row.declared_closing_minor,
                                        row.currency,
                                    )
                                }}
                            </p>
                            <div
                                class="mt-5 flex items-end justify-between gap-3 border-t border-white/10 pt-4 dark:border-neutral-950/10"
                            >
                                <span
                                    class="text-xs text-neutral-400 dark:text-neutral-600"
                                    >{{
                                        t('cash.reconciliation.difference')
                                    }}</span
                                ><strong
                                    class="tabular-nums"
                                    :class="
                                        row.difference_minor === 0
                                            ? 'text-success-300 dark:text-success-700'
                                            : row.difference_minor < 0
                                              ? 'text-danger-300 dark:text-danger-700'
                                              : 'text-secondary-300 dark:text-secondary-800'
                                    "
                                    >{{
                                        money(
                                            row.difference_minor,
                                            row.currency,
                                        )
                                    }}</strong
                                >
                            </div>
                        </article>
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
                        <article
                            v-for="movement in movements.data"
                            :key="movement.id"
                            class="grid gap-3 px-5 py-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center sm:px-7"
                        >
                            <div class="min-w-0">
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t(`cash.types.${movement.type}`) }}
                                </p>
                                <p
                                    class="mt-1 text-sm break-words text-neutral-500 dark:text-neutral-400"
                                >
                                    {{
                                        movement.comment ??
                                        t('cash.summary.automatic')
                                    }}
                                    · {{ dateTime(movement.occurred_at) }}
                                </p>
                            </div>
                            <strong
                                class="shrink-0 tabular-nums"
                                :class="
                                    movement.direction === 'in'
                                        ? 'text-success-700 dark:text-success-400'
                                        : 'text-danger-700 dark:text-danger-400'
                                "
                                >{{ movement.direction === 'in' ? '+' : '-'
                                }}{{
                                    money(
                                        movement.amount_minor,
                                        movement.currency,
                                    )
                                }}</strong
                            >
                        </article>
                    </div>
                    <p
                        v-else
                        class="px-5 py-10 text-center text-sm text-neutral-500 sm:px-7 dark:text-neutral-400"
                    >
                        {{ t('cash.summary.empty_shift') }}
                    </p>
                    <Pagination
                        v-if="movements.data.length"
                        :pagination="movements"
                    />
                </section>
            </div>
        </div>
    </DefaultLayout>
</template>
