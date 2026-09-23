<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowRightIcon,
    CheckIcon,
    NoSymbolIcon,
    PencilSquareIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    reservation: Object,
    quote: Object,
    currency: String,
});
const { t, locale } = useI18n();
const pendingAction = ref(null);
const actionForm = useForm({});
const isEditable = computed(() =>
    ['draft', 'confirmed'].includes(props.reservation.status),
);
const canConfirm = computed(() => props.reservation.status === 'draft');
const canCheckIn = computed(
    () =>
        props.reservation.status === 'confirmed' &&
        props.reservation.planned_check_in_on <=
            new Date().toISOString().slice(0, 10),
);
const canNoShow = canCheckIn;

function formatMoney(value) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: props.currency,
    }).format(Number(value));
}
function actionRoute(action) {
    return route(`hotels.reservations.${action}`, [
        props.hotel.id,
        props.reservation.id,
    ]);
}
function submitAction() {
    const action = pendingAction.value;
    const method = action === 'check-in' ? 'post' : 'patch';
    actionForm[method](actionRoute(action), {
        preserveScroll: true,
        onSuccess: () => (pendingAction.value = null),
    });
}
</script>

<template>
    <Head
        :title="t('reservations.pages.show.heading', { id: reservation.id })"
    />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="
                        t('reservations.pages.show.heading', {
                            id: reservation.id,
                        })
                    "
                    :description="`${reservation.responsible_guest.first_name} ${reservation.responsible_guest.last_name}`"
                >
                    <template #actions>
                        <span
                            class="rounded-full bg-neutral-100 px-3 py-2 text-sm font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                            >{{
                                t(`reservations.statuses.${reservation.status}`)
                            }}</span
                        >
                        <ActionLink
                            v-if="isEditable"
                            :href="
                                route('hotels.reservations.edit', [
                                    hotel.id,
                                    reservation.id,
                                ])
                            "
                            variant="secondary"
                            ><PencilSquareIcon class="h-4 w-4" />{{
                                t('reservations.actions.edit')
                            }}</ActionLink
                        >
                    </template>
                </PageHeader>

                <section
                    class="grid overflow-hidden rounded-2xl bg-white shadow-sm lg:grid-cols-[minmax(0,1.2fr)_minmax(18rem,0.8fr)] dark:bg-neutral-900"
                >
                    <div class="p-5 sm:p-7">
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('reservations.pages.show.plan') }}
                        </h2>
                        <dl class="mt-5 grid gap-5 sm:grid-cols-3">
                            <div>
                                <dt
                                    class="text-xs font-semibold text-neutral-500"
                                >
                                    {{ t('reservations.fields.check_in') }}
                                </dt>
                                <dd
                                    class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ reservation.planned_check_in_on }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs font-semibold text-neutral-500"
                                >
                                    {{ t('reservations.fields.check_out') }}
                                </dt>
                                <dd
                                    class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ reservation.planned_check_out_on }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs font-semibold text-neutral-500"
                                >
                                    {{ t('reservations.fields.nights') }}
                                </dt>
                                <dd
                                    class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ quote.nights }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div
                        class="border-t border-neutral-200 bg-neutral-50 p-5 sm:p-7 lg:border-s lg:border-t-0 dark:border-neutral-800 dark:bg-neutral-950"
                    >
                        <p
                            class="text-sm font-medium text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('reservations.fields.quote') }}
                        </p>
                        <p
                            class="mt-2 text-3xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                        >
                            {{ formatMoney(quote.total) }}
                        </p>
                        <p
                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('reservations.pages.show.quote_note') }}
                        </p>
                    </div>
                </section>

                <section
                    v-if="reservation.stay"
                    class="grid overflow-hidden rounded-2xl bg-neutral-900 text-white shadow-sm lg:grid-cols-2 dark:bg-white dark:text-neutral-950"
                >
                    <div class="p-5 sm:p-7">
                        <h2 class="text-lg font-semibold">
                            {{ t('reservations.pages.show.reserved_plan') }}
                        </h2>
                        <p
                            class="mt-3 text-sm text-neutral-300 dark:text-neutral-600"
                        >
                            {{ reservation.planned_check_in_on }} →
                            {{ reservation.planned_check_out_on }}
                        </p>
                    </div>
                    <div
                        class="border-t border-neutral-700 p-5 sm:p-7 lg:border-s lg:border-t-0 dark:border-neutral-200"
                    >
                        <h2 class="text-lg font-semibold">
                            {{ t('reservations.pages.show.current_stay') }}
                        </h2>
                        <p
                            class="mt-3 text-sm text-neutral-300 dark:text-neutral-600"
                        >
                            {{ reservation.stay.checked_in_at }} →
                            {{ reservation.stay.expected_check_out_on }}
                        </p>
                        <ActionLink
                            :href="
                                route('hotels.stays.show', [
                                    hotel.id,
                                    reservation.stay.id,
                                ])
                            "
                            variant="secondary"
                            size="sm"
                            class="mt-5"
                            >{{ t('reservations.pages.show.open_stay')
                            }}<ArrowRightIcon class="h-4 w-4"
                        /></ActionLink>
                    </div>
                </section>

                <section
                    v-if="isEditable"
                    class="flex flex-wrap gap-3 rounded-2xl bg-white p-5 shadow-sm sm:p-7 dark:bg-neutral-900"
                >
                    <PrimaryButton
                        v-if="canConfirm"
                        type="button"
                        @click="pendingAction = 'confirm'"
                        ><CheckIcon class="h-4 w-4" />{{
                            t('reservations.actions.confirm')
                        }}</PrimaryButton
                    >
                    <PrimaryButton
                        v-if="canCheckIn"
                        type="button"
                        @click="pendingAction = 'check-in'"
                        ><ArrowRightIcon class="h-4 w-4" />{{
                            t('reservations.actions.check_in')
                        }}</PrimaryButton
                    >
                    <SecondaryButton
                        v-if="canNoShow"
                        type="button"
                        @click="pendingAction = 'no-show'"
                        ><NoSymbolIcon class="h-4 w-4" />{{
                            t('reservations.actions.no_show')
                        }}</SecondaryButton
                    >
                    <SecondaryButton
                        type="button"
                        class="text-danger-700 dark:text-danger-300"
                        @click="pendingAction = 'cancel'"
                        ><XMarkIcon class="h-4 w-4" />{{
                            t('reservations.actions.cancel')
                        }}</SecondaryButton
                    >
                </section>

                <div
                    class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(20rem,0.8fr)]"
                >
                    <section
                        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    >
                        <div
                            class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                        >
                            <h2
                                class="text-lg font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('reservations.pages.show.rooms') }}
                            </h2>
                        </div>
                        <ul
                            class="divide-y divide-neutral-200 dark:divide-neutral-800"
                        >
                            <li
                                v-for="reservedRoom in reservation.reserved_rooms"
                                :key="reservedRoom.id"
                                class="grid gap-3 px-5 py-5 sm:grid-cols-[minmax(0,1fr)_auto] sm:px-7"
                            >
                                <div>
                                    <p
                                        class="font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{ reservedRoom.room.number }} ·
                                        {{ reservedRoom.room.room_type.name }}
                                    </p>
                                    <p
                                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{
                                            reservedRoom.reservation_guests
                                                .map(
                                                    (item) =>
                                                        `${item.guest.first_name} ${item.guest.last_name}`,
                                                )
                                                .join(', ')
                                        }}
                                    </p>
                                </div>
                                <p
                                    class="font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ formatMoney(reservedRoom.nightly_rate) }}
                                    / {{ t('reservations.form.rooms.night') }}
                                </p>
                            </li>
                        </ul>
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
                                {{ t('reservations.pages.show.history') }}
                            </h2>
                        </div>
                        <ol
                            class="divide-y divide-neutral-200 dark:divide-neutral-800"
                        >
                            <li
                                v-for="event in reservation.events"
                                :key="event.id"
                                class="px-5 py-4 sm:px-7"
                            >
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t(`reservations.events.${event.type}`) }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                >
                                    {{ event.created_at }}
                                </p>
                            </li>
                        </ol>
                    </section>
                </div>
            </div>
        </div>

        <Modal
            :show="Boolean(pendingAction)"
            max-width="lg"
            @close="pendingAction = null"
        >
            <form
                v-if="pendingAction"
                class="grid gap-5 p-6"
                @submit.prevent="submitAction"
            >
                <div>
                    <h2
                        class="text-xl font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t(`reservations.dialogs.${pendingAction}.title`) }}
                    </h2>
                    <p
                        class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{
                            t(
                                `reservations.dialogs.${pendingAction}.description`,
                            )
                        }}
                    </p>
                </div>
                <InputError
                    :message="
                        actionForm.errors.reservation ||
                        actionForm.errors.reserved_rooms
                    "
                />
                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        type="button"
                        @click="pendingAction = null"
                        >{{ t('app.cancel') }}</SecondaryButton
                    ><PrimaryButton
                        type="submit"
                        :disabled="actionForm.processing"
                        >{{
                            t(`reservations.dialogs.${pendingAction}.action`)
                        }}</PrimaryButton
                    >
                </div>
            </form>
        </Modal>
    </DefaultLayout>
</template>
