<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import GuestFields from '@/Pages/Hotels/Guests/Components/GuestFields.vue';
import GuestLookupField from '@/Pages/Hotels/Guests/Components/GuestLookupField.vue';
import {
    ArrowPathIcon,
    ArrowsRightLeftIcon,
    CheckIcon,
    UserPlusIcon,
} from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    stay: Object,
    stayCostSummary: Object,
    rooms: Array,
    identificationTypes: Array,
    currency: String,
});
const { t, locale } = useI18n();
const isTransferOpen = ref(false);
const isGuestModalOpen = ref(false);
const selectedOccupancy = ref(null);
const expectedCheckOutForm = useForm({
    expected_check_out_on: props.stay.expected_check_out_on,
});
const checkOutForm = useForm({});
const transferForm = useForm({ room_id: '', nightly_rate: '' });
const guestForm = useForm({
    room_occupancy_id: '',
    guest_id: null,
    first_name: '',
    last_name: '',
    identification_type_id: '',
    identification_number: '',
    mobile: '',
    email: '',
});
const isActive = computed(() => props.stay.status === 'active');
const availableOccupancies = computed(() =>
    props.stay.room_occupancies.filter(
        (occupancy) =>
            !occupancy.checked_out_at &&
            occupancy.guests.length < occupancy.room.room_type.capacity,
    ),
);
const stayGuestIds = computed(() =>
    props.stay.stay_guests.map((stayGuest) => stayGuest.guest.id),
);
const costItemsByOccupancyId = computed(() =>
    Object.fromEntries(
        props.stayCostSummary.items.map((item) => [
            item.room_occupancy_id,
            item,
        ]),
    ),
);

function formatMoney(value) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: props.currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value));
}

function formatNights(count) {
    return t('stays.pages.show.costs.nights_count', { count });
}

function updateExpectedCheckOut() {
    expectedCheckOutForm.patch(
        route('hotels.stays.expected-check-out.update', [
            props.hotel.id,
            props.stay.id,
        ]),
    );
}

function checkOut() {
    checkOutForm.post(
        route('hotels.stays.check-out', [props.hotel.id, props.stay.id]),
    );
}

function openTransfer(occupancy) {
    selectedOccupancy.value = occupancy;
    transferForm.reset();
    isTransferOpen.value = true;
}

function setTransferRate() {
    const room = props.rooms.find(
        (item) => Number(item.id) === Number(transferForm.room_id),
    );
    transferForm.nightly_rate = room?.reference_price ?? '';
}

function transfer() {
    transferForm.post(
        route('hotels.stays.room-occupancies.transfer', [
            props.hotel.id,
            props.stay.id,
            selectedOccupancy.value.id,
        ]),
        {
            onSuccess: () => {
                isTransferOpen.value = false;
            },
        },
    );
}

function resetGuestForm() {
    guestForm.reset();
    guestForm.clearErrors();
}

function openGuestModal() {
    resetGuestForm();
    isGuestModalOpen.value = true;
}

function closeGuestModal() {
    isGuestModalOpen.value = false;
    resetGuestForm();
}

function selectGuest(guest) {
    Object.assign(guestForm, {
        guest_id: guest.id,
        first_name: guest.first_name,
        last_name: guest.last_name,
        identification_type_id: guest.identification_type_id,
        identification_number: guest.identification_number,
        mobile: guest.mobile ?? '',
        email: guest.email ?? '',
    });
}

function registerNewGuest() {
    const roomOccupancyId = guestForm.room_occupancy_id;

    resetGuestForm();
    guestForm.room_occupancy_id = roomOccupancyId;
}

function addGuest() {
    guestForm.post(
        route('hotels.stays.guests.store', [props.hotel.id, props.stay.id]),
        {
            preserveScroll: true,
            onSuccess: closeGuestModal,
        },
    );
}
</script>

<template>
    <Head :title="t('stays.pages.show.heading')" />

    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('stays.pages.show.heading')"
                    :description="`${stay.responsible_guest.first_name} ${stay.responsible_guest.last_name}`"
                >
                    <template #actions>
                        <span
                            class="rounded-full px-3 py-2 text-sm font-semibold"
                            :class="
                                isActive
                                    ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                                    : 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300'
                            "
                            >{{
                                isActive
                                    ? t('stays.pages.show.active')
                                    : t('stays.pages.show.checked_out')
                            }}</span
                        >
                    </template>
                </PageHeader>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="grid gap-6 p-5 sm:p-7 lg:grid-cols-[minmax(18rem,0.75fr)_minmax(0,1.25fr)] lg:gap-0"
                    >
                        <div class="grid content-start gap-5 lg:pr-7">
                            <div>
                                <h2
                                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.pages.show.costs.heading') }}
                                </h2>
                                <p
                                    class="mt-1 max-w-prose text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        stayCostSummary.is_estimate
                                            ? t(
                                                  'stays.pages.show.costs.estimate_description',
                                              )
                                            : t(
                                                  'stays.pages.show.costs.final_description',
                                              )
                                    }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-sm font-medium text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        stayCostSummary.is_estimate
                                            ? t(
                                                  'stays.pages.show.costs.estimated_total',
                                              )
                                            : t('stays.pages.show.costs.total')
                                    }}
                                </p>
                                <p
                                    class="mt-2 text-3xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{
                                        formatMoney(
                                            stayCostSummary.total_amount,
                                        )
                                    }}
                                </p>
                            </div>

                            <dl
                                class="grid grid-cols-2 gap-4 border-t border-neutral-200 pt-5 dark:border-neutral-800"
                            >
                                <div>
                                    <dt
                                        class="text-xs font-medium text-neutral-500 dark:text-neutral-500"
                                    >
                                        {{
                                            t(
                                                'stays.pages.show.costs.billable_nights',
                                            )
                                        }}
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-semibold text-neutral-950 tabular-nums dark:text-white"
                                    >
                                        {{
                                            formatNights(
                                                stayCostSummary.total_nights,
                                            )
                                        }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-xs font-medium text-neutral-500 dark:text-neutral-500"
                                    >
                                        {{
                                            t('stays.pages.show.costs.currency')
                                        }}
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-semibold text-neutral-950 tabular-nums dark:text-white"
                                    >
                                        {{ currency }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div
                            class="border-t border-neutral-200 pt-5 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-7 dark:border-neutral-800"
                        >
                            <div
                                class="grid grid-cols-[minmax(0,1fr)_auto] gap-4 pb-3 text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                            >
                                <span>{{
                                    t('stays.pages.show.costs.room_period')
                                }}</span>
                                <span>{{
                                    t('stays.pages.show.costs.subtotal')
                                }}</span>
                            </div>
                            <ul
                                class="divide-y divide-neutral-200 border-t border-neutral-200 dark:divide-neutral-800 dark:border-neutral-800"
                            >
                                <li
                                    v-for="occupancy in stay.room_occupancies"
                                    :key="`cost-${occupancy.id}`"
                                    class="grid grid-cols-[minmax(0,1fr)_auto] gap-4 py-4"
                                >
                                    <div class="min-w-0">
                                        <p
                                            class="font-medium text-neutral-950 dark:text-white"
                                        >
                                            {{ occupancy.room.number }} ·
                                            {{ occupancy.room.room_type.name }}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                        >
                                            {{
                                                costItemsByOccupancyId[
                                                    occupancy.id
                                                ].period_start_on
                                            }}
                                            -
                                            {{
                                                costItemsByOccupancyId[
                                                    occupancy.id
                                                ].period_end_on
                                            }}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                        >
                                            {{
                                                formatNights(
                                                    costItemsByOccupancyId[
                                                        occupancy.id
                                                    ].billable_nights,
                                                )
                                            }}
                                            ·
                                            {{
                                                formatMoney(
                                                    costItemsByOccupancyId[
                                                        occupancy.id
                                                    ].nightly_rate,
                                                )
                                            }}
                                            {{
                                                t(
                                                    'stays.pages.show.costs.per_night',
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <p
                                        class="text-right font-semibold text-neutral-950 tabular-nums dark:text-white"
                                    >
                                        {{
                                            formatMoney(
                                                costItemsByOccupancyId[
                                                    occupancy.id
                                                ].subtotal_amount,
                                            )
                                        }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section
                    v-if="isActive"
                    class="grid gap-5 rounded-2xl bg-white p-5 shadow-sm sm:p-7 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end dark:bg-neutral-900"
                >
                    <form
                        class="grid gap-2"
                        @submit.prevent="updateExpectedCheckOut"
                    >
                        <InputLabel
                            for="expected-check-out"
                            :value="
                                t('stays.fields.expected_check_out_on.label')
                            "
                            required
                        />
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <TextInput
                                id="expected-check-out"
                                v-model="
                                    expectedCheckOutForm.expected_check_out_on
                                "
                                type="date"
                                class="w-full sm:max-w-xs"
                                :invalid="
                                    Boolean(
                                        expectedCheckOutForm.errors
                                            .expected_check_out_on,
                                    )
                                "
                            /><PrimaryButton
                                type="submit"
                                :disabled="expectedCheckOutForm.processing"
                                ><ArrowPathIcon class="h-4 w-4" />{{
                                    t('stays.actions.extend')
                                }}</PrimaryButton
                            >
                        </div>
                        <InputError
                            :message="
                                expectedCheckOutForm.errors
                                    .expected_check_out_on
                            "
                        />
                    </form>
                    <PrimaryButton
                        type="button"
                        :disabled="checkOutForm.processing"
                        @click="checkOut"
                        ><CheckIcon class="h-4 w-4" />{{
                            t('stays.actions.check_out')
                        }}</PrimaryButton
                    >
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
                            {{ t('stays.pages.show.occupancies') }}
                        </h2>
                    </div>
                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="occupancy in stay.room_occupancies"
                            :key="occupancy.id"
                            class="grid gap-4 px-5 py-5 sm:px-7 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center"
                        >
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p
                                        class="text-base font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{ occupancy.room.number }} ·
                                        {{ occupancy.room.room_type.name }}
                                    </p>
                                    <span
                                        v-if="occupancy.checked_out_at"
                                        class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                                        >{{
                                            occupancy.end_reason === 'transfer'
                                                ? t('stays.actions.transfer')
                                                : t(
                                                      'stays.pages.show.checked_out',
                                                  )
                                        }}</span
                                    >
                                </div>
                                <p
                                    class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        occupancy.guests
                                            .map(
                                                (guest) =>
                                                    `${guest.first_name} ${guest.last_name}`,
                                            )
                                            .join(', ')
                                    }}
                                </p>
                                <p
                                    class="mt-2 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                >
                                    {{ formatMoney(occupancy.nightly_rate) }}
                                    {{ currency }} ·
                                    {{ occupancy.expected_check_out_on }}
                                </p>
                            </div>
                            <SecondaryButton
                                v-if="isActive && !occupancy.checked_out_at"
                                type="button"
                                @click="openTransfer(occupancy)"
                                ><ArrowsRightLeftIcon class="h-4 w-4" />{{
                                    t('stays.actions.transfer')
                                }}</SecondaryButton
                            >
                        </li>
                    </ul>
                </section>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <div>
                            <h2
                                class="text-lg font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('stays.pages.show.group') }}
                            </h2>
                            <p
                                v-if="isActive && !availableOccupancies.length"
                                class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('stays.pages.show.no_room_capacity') }}
                            </p>
                        </div>
                        <PrimaryButton
                            v-if="isActive"
                            type="button"
                            :disabled="!availableOccupancies.length"
                            @click="openGuestModal"
                        >
                            <UserPlusIcon class="h-4 w-4" />
                            {{ t('stays.actions.add_guest') }}
                        </PrimaryButton>
                    </div>
                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="stayGuest in stay.stay_guests"
                            :key="stayGuest.id"
                            class="flex items-center justify-between gap-4 px-5 py-4 sm:px-7"
                        >
                            <span
                                class="font-medium text-neutral-950 dark:text-white"
                                >{{ stayGuest.guest.first_name }}
                                {{ stayGuest.guest.last_name }}</span
                            ><span
                                class="text-sm text-neutral-600 dark:text-neutral-400"
                                >{{
                                    stayGuest.role === 'responsible'
                                        ? t('stays.form.guests.responsible')
                                        : t('stays.form.guests.companion')
                                }}</span
                            >
                        </li>
                    </ul>
                </section>
            </div>
        </div>

        <Modal
            :show="isGuestModalOpen"
            max-width="2xl"
            @close="closeGuestModal"
        >
            <form class="grid gap-6 p-6" @submit.prevent="addGuest">
                <div>
                    <h2
                        class="text-xl font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('stays.actions.add_guest') }}
                    </h2>
                    <p
                        class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('stays.pages.show.add_guest_description') }}
                    </p>
                </div>

                <div class="grid gap-2">
                    <InputLabel
                        for="guest-room-occupancy"
                        :value="t('stays.form.rooms.assign_label')"
                        required
                    />
                    <select
                        id="guest-room-occupancy"
                        v-model="guestForm.room_occupancy_id"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    >
                        <option value="" disabled>
                            {{ t('stays.form.rooms.assign_placeholder') }}
                        </option>
                        <option
                            v-for="occupancy in availableOccupancies"
                            :key="occupancy.id"
                            :value="occupancy.id"
                        >
                            {{ occupancy.room.number }} ·
                            {{ occupancy.room.room_type.name }} ·
                            {{ occupancy.guests.length }}/{{
                                occupancy.room.room_type.capacity
                            }}
                        </option>
                    </select>
                    <InputError :message="guestForm.errors.room_occupancy_id" />
                </div>

                <div v-if="guestForm.guest_id" class="grid gap-3">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-neutral-200 p-4 dark:border-neutral-800"
                    >
                        <div>
                            <p
                                class="font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ guestForm.first_name }}
                                {{ guestForm.last_name }}
                            </p>
                            <p
                                class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                            >
                                {{ guestForm.identification_number }}
                            </p>
                        </div>
                        <button
                            type="button"
                            class="text-primary-700 hover:text-primary-800 dark:text-primary-300 text-sm font-semibold"
                            @click="registerNewGuest"
                        >
                            {{ t('stays.form.guests.new_guest') }}
                        </button>
                    </div>
                    <InputError :message="guestForm.errors.guest_id" />
                </div>

                <GuestLookupField
                    v-else
                    :hotel="hotel"
                    :excluded-guest-ids="stayGuestIds"
                    id="stay-guest-search"
                    @select="selectGuest"
                />

                <GuestFields
                    :guest="guestForm"
                    :identification-types="identificationTypes"
                    :errors="guestForm.errors"
                    id-prefix="stay-guest"
                    :disabled="Boolean(guestForm.guest_id)"
                    @update:guest="Object.assign(guestForm, $event)"
                />

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton type="button" @click="closeGuestModal">
                        {{ t('app.cancel') }}
                    </SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        :disabled="guestForm.processing"
                    >
                        <UserPlusIcon class="h-4 w-4" />
                        {{ t('stays.actions.add_guest') }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <Modal
            :show="isTransferOpen"
            max-width="lg"
            @close="isTransferOpen = false"
            ><form class="grid gap-5 p-6" @submit.prevent="transfer">
                <div>
                    <h2
                        class="text-xl font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('stays.actions.transfer') }}
                    </h2>
                    <p
                        class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ selectedOccupancy?.room.number }}
                    </p>
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="transfer-room"
                        :value="t('stays.fields.room.label')"
                        required
                    /><select
                        id="transfer-room"
                        v-model="transferForm.room_id"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                        @change="setTransferRate"
                    >
                        <option value="" disabled>
                            {{ t('stays.form.rooms.select_room') }}
                        </option>
                        <option
                            v-for="room in rooms"
                            :key="room.id"
                            :value="room.id"
                        >
                            {{ room.number }} · {{ room.room_type.name }}
                        </option></select
                    ><InputError :message="transferForm.errors.room_id" />
                </div>
                <div class="grid gap-2">
                    <InputLabel
                        for="transfer-rate"
                        :value="t('stays.fields.nightly_rate.label')"
                        required
                    /><TextInput
                        id="transfer-rate"
                        v-model="transferForm.nightly_rate"
                        type="number"
                        min="0.01"
                        step="0.01"
                        class="w-full"
                        :invalid="Boolean(transferForm.errors.nightly_rate)"
                    /><InputError :message="transferForm.errors.nightly_rate" />
                </div>
                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        type="button"
                        @click="isTransferOpen = false"
                        >{{ t('app.cancel') }}</SecondaryButton
                    ><PrimaryButton
                        type="submit"
                        :disabled="transferForm.processing"
                        >{{ t('stays.actions.transfer') }}</PrimaryButton
                    >
                </div>
            </form></Modal
        >
    </DefaultLayout>
</template>
