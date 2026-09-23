<script setup>
import { CalendarDaysIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    plannedCheckInOn: { type: String, required: true },
    plannedCheckOutOn: { type: String, required: true },
    nights: { type: Number, required: true },
    guestCount: { type: Number, required: true },
    roomCount: { type: Number, required: true },
    quotedTotal: { type: Number, required: true },
    currency: { type: String, required: true },
});

const { t, locale } = useI18n();

function formatMoney(value) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: props.currency,
    }).format(Number(value));
}
</script>

<template>
    <aside
        class="rounded-2xl bg-neutral-900 p-5 text-white shadow-sm xl:sticky xl:top-6 dark:bg-white dark:text-neutral-950"
    >
        <div class="flex items-center gap-3">
            <CalendarDaysIcon
                class="text-primary-400 dark:text-primary-600 h-5 w-5"
            />
            <h2 class="font-semibold">
                {{ t('reservations.form.summary.title') }}
            </h2>
        </div>
        <dl class="mt-5 grid gap-4 text-sm">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <dt class="text-neutral-300 dark:text-neutral-600">
                        {{ t('reservations.fields.check_in') }}
                    </dt>
                    <dd class="mt-1 font-semibold tabular-nums">
                        {{ plannedCheckInOn }}
                    </dd>
                </div>
                <div>
                    <dt class="text-neutral-300 dark:text-neutral-600">
                        {{ t('reservations.fields.check_out') }}
                    </dt>
                    <dd class="mt-1 font-semibold tabular-nums">
                        {{ plannedCheckOutOn }}
                    </dd>
                </div>
            </div>
            <div
                class="grid grid-cols-3 gap-3 border-t border-neutral-700 pt-4 dark:border-neutral-200"
            >
                <div>
                    <dt class="text-neutral-300 dark:text-neutral-600">
                        {{ t('reservations.fields.nights') }}
                    </dt>
                    <dd class="mt-1 font-semibold tabular-nums">
                        {{ nights }}
                    </dd>
                </div>
                <div>
                    <dt class="text-neutral-300 dark:text-neutral-600">
                        {{ t('reservations.fields.guests') }}
                    </dt>
                    <dd class="mt-1 font-semibold tabular-nums">
                        {{ guestCount }}
                    </dd>
                </div>
                <div>
                    <dt class="text-neutral-300 dark:text-neutral-600">
                        {{ t('reservations.fields.rooms') }}
                    </dt>
                    <dd class="mt-1 font-semibold tabular-nums">
                        {{ roomCount }}
                    </dd>
                </div>
            </div>
            <div
                class="border-t border-neutral-700 pt-4 dark:border-neutral-200"
            >
                <dt class="text-neutral-300 dark:text-neutral-600">
                    {{ t('reservations.fields.quote') }}
                </dt>
                <dd class="mt-2 text-2xl font-semibold tabular-nums">
                    {{ formatMoney(quotedTotal) }}
                </dd>
            </div>
        </dl>
    </aside>
</template>
