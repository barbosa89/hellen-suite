<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestFields from '@/Pages/Hotels/Guests/Components/GuestFields.vue';
import GuestLookupField from '@/Pages/Hotels/Guests/Components/GuestLookupField.vue';
import {
    BuildingOffice2Icon,
    CalendarDaysIcon,
    CheckCircleIcon,
    CheckIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    UserPlusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: { type: Object, required: true },
    reservation: { type: Object, default: null },
    identificationTypes: { type: Array, required: true },
    currency: { type: String, required: true },
});
const { t, locale } = useI18n();
const currentStep = ref(1);
const flowError = ref('');
const rooms = ref(
    props.reservation?.reserved_rooms?.map((item) => item.room) ?? [],
);
const loadingRooms = ref(false);
let availabilityRequest;

const steps = computed(() => [
    t('reservations.form.steps.dates'),
    t('reservations.form.steps.guests'),
    t('reservations.form.steps.rooms'),
    t('reservations.form.steps.review'),
]);

function blankGuest(
    key = `guest-${globalThis.crypto?.randomUUID?.() ?? Date.now()}`,
) {
    return {
        key,
        guest_id: null,
        first_name: '',
        last_name: '',
        identification_type_id: '',
        identification_number: '',
        mobile: '',
        email: '',
    };
}

function initialGuests() {
    if (!props.reservation) {
        return [blankGuest('responsible')];
    }

    return props.reservation.reservation_guests.map((entry) => ({
        key: `reservation-guest-${entry.id}`,
        guest_id: entry.guest.id,
        first_name: entry.guest.first_name,
        last_name: entry.guest.last_name,
        identification_type_id: entry.guest.identification_type_id,
        identification_number: entry.guest.identification_number,
        mobile: entry.guest.mobile ?? '',
        email: entry.guest.email ?? '',
    }));
}

function initialReservedRooms() {
    if (!props.reservation) return [];

    return props.reservation.reserved_rooms.map((reservedRoom) => ({
        room_id: reservedRoom.room_id,
        nightly_rate: reservedRoom.nightly_rate,
        guest_keys: reservedRoom.reservation_guests.map(
            (reservationGuest) => `reservation-guest-${reservationGuest.id}`,
        ),
    }));
}

const form = useForm({
    planned_check_in_on:
        props.reservation?.planned_check_in_on ??
        new Date(Date.now() + 86400000).toISOString().slice(0, 10),
    planned_check_out_on:
        props.reservation?.planned_check_out_on ??
        new Date(Date.now() + 172800000).toISOString().slice(0, 10),
    responsible_guest_key: props.reservation
        ? `reservation-guest-${props.reservation.reservation_guests.find((entry) => entry.role === 'responsible')?.id}`
        : 'responsible',
    guests: initialGuests(),
    reserved_rooms: initialReservedRooms(),
});

const selectedRoomIds = computed(() =>
    form.reserved_rooms.map((item) => Number(item.room_id)),
);
const selectedRooms = computed(() =>
    form.reserved_rooms
        .map((item) => ({
            selection: item,
            room: rooms.value.find(
                (room) => Number(room.id) === Number(item.room_id),
            ),
        }))
        .filter((item) => item.room),
);
const nights = computed(() => {
    const start = new Date(`${form.planned_check_in_on}T00:00:00`);
    const end = new Date(`${form.planned_check_out_on}T00:00:00`);
    return Math.max(0, Math.round((end - start) / 86400000));
});
const quotedTotal = computed(() =>
    form.reserved_rooms.reduce(
        (total, room) => total + Number(room.nightly_rate || 0) * nights.value,
        0,
    ),
);

function roomSelection(roomId) {
    return form.reserved_rooms.find(
        (item) => Number(item.room_id) === Number(roomId),
    );
}
function assignedRoomId(guest) {
    return (
        form.reserved_rooms.find((room) => room.guest_keys.includes(guest.key))
            ?.room_id ?? ''
    );
}
function guestName(guest) {
    return (
        `${guest.first_name} ${guest.last_name}`.trim() ||
        t('reservations.form.guests.unnamed')
    );
}
function guestCount(roomId) {
    return roomSelection(roomId)?.guest_keys.length ?? 0;
}
function formatMoney(value) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: props.currency,
    }).format(Number(value));
}

async function loadRooms() {
    if (!form.planned_check_in_on || !form.planned_check_out_on) return;
    if (form.planned_check_out_on <= form.planned_check_in_on) return;

    availabilityRequest?.abort();
    availabilityRequest = new AbortController();
    loadingRooms.value = true;

    try {
        const parameters = new URLSearchParams({
            planned_check_in_on: form.planned_check_in_on,
            planned_check_out_on: form.planned_check_out_on,
        });
        const routeParameters = props.reservation
            ? [props.hotel.id, props.reservation.id]
            : [props.hotel.id];
        const response = await fetch(
            `${route('hotels.reservations.availability', routeParameters)}?${parameters}`,
            { signal: availabilityRequest.signal },
        );
        const data = response.ok ? await response.json() : { rooms: [] };
        rooms.value = data.rooms ?? [];
        const availableIds = rooms.value.map((room) => Number(room.id));
        form.reserved_rooms = form.reserved_rooms.filter((item) =>
            availableIds.includes(Number(item.room_id)),
        );
    } catch (error) {
        if (error.name !== 'AbortError') rooms.value = [];
    } finally {
        loadingRooms.value = false;
    }
}

function addCompanion() {
    form.guests.push(blankGuest());
}
function removeGuest(index) {
    const [removed] = form.guests.splice(index, 1);
    form.reserved_rooms.forEach((room) => {
        room.guest_keys = room.guest_keys.filter((key) => key !== removed.key);
    });
}
function selectGuest(entry, guest) {
    Object.assign(entry, {
        key: entry.key,
        guest_id: guest.id,
        first_name: guest.first_name,
        last_name: guest.last_name,
        identification_type_id: guest.identification_type_id,
        identification_number: guest.identification_number,
        mobile: guest.mobile ?? '',
        email: guest.email ?? '',
    });
}
function registerNew(entry) {
    Object.assign(entry, blankGuest(entry.key));
}
function toggleRoom(room) {
    const index = form.reserved_rooms.findIndex(
        (item) => Number(item.room_id) === Number(room.id),
    );
    if (index >= 0) {
        form.reserved_rooms.splice(index, 1);
    } else {
        form.reserved_rooms.push({
            room_id: room.id,
            nightly_rate: room.reference_price,
            guest_keys: [],
        });
    }
}
function assignGuest(guest, roomId) {
    form.reserved_rooms.forEach((room) => {
        room.guest_keys = room.guest_keys.filter((key) => key !== guest.key);
    });
    roomSelection(roomId)?.guest_keys.push(guest.key);
}

function validateStep() {
    flowError.value = '';
    if (
        currentStep.value === 1 &&
        (!form.planned_check_in_on ||
            !form.planned_check_out_on ||
            form.planned_check_out_on <= form.planned_check_in_on)
    ) {
        flowError.value = t('reservations.form.errors.dates');
        return false;
    }
    if (
        currentStep.value === 2 &&
        form.guests.some(
            (guest) =>
                !guest.guest_id &&
                (!guest.first_name ||
                    !guest.last_name ||
                    !guest.identification_type_id ||
                    !guest.identification_number),
        )
    ) {
        flowError.value = t('reservations.form.errors.guests');
        return false;
    }
    if (
        currentStep.value === 3 &&
        (!form.reserved_rooms.length ||
            form.guests.some((guest) => !assignedRoomId(guest)))
    ) {
        flowError.value = t('reservations.form.errors.rooms');
        return false;
    }
    return true;
}
function nextStep() {
    if (validateStep()) currentStep.value += 1;
}
function previousStep() {
    flowError.value = '';
    currentStep.value -= 1;
}
function submit() {
    const options = {
        onError: (errors) => {
            currentStep.value = Object.keys(errors).some((key) =>
                key.startsWith('reserved_rooms'),
            )
                ? 3
                : Object.keys(errors).some((key) => key.startsWith('guests'))
                  ? 2
                  : 1;
        },
    };
    if (props.reservation) {
        form.patch(
            route('hotels.reservations.update', [
                props.hotel.id,
                props.reservation.id,
            ]),
            options,
        );
    } else {
        form.post(route('hotels.reservations.store', props.hotel.id), options);
    }
}

watch(() => [form.planned_check_in_on, form.planned_check_out_on], loadRooms);
onMounted(loadRooms);
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <nav
            class="rounded-2xl bg-white p-2 shadow-sm dark:bg-neutral-900"
            :aria-label="t('reservations.form.steps.label')"
        >
            <ol class="grid gap-1 sm:grid-cols-4">
                <li v-for="(step, index) in steps" :key="step">
                    <button
                        type="button"
                        class="focus-visible:ring-primary-500 flex min-h-12 w-full items-center gap-3 rounded-xl px-3 text-start text-sm font-semibold focus-visible:ring-2 focus-visible:outline-hidden disabled:cursor-default"
                        :class="
                            currentStep === index + 1
                                ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                                : index + 1 < currentStep
                                  ? 'text-neutral-800 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800'
                                  : 'text-neutral-500'
                        "
                        :disabled="index + 1 > currentStep"
                        @click="currentStep = index + 1"
                    >
                        <span
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs tabular-nums dark:bg-neutral-800"
                            >{{ index + 1 }}</span
                        >{{ step }}
                    </button>
                </li>
            </ol>
        </nav>

        <p
            v-if="flowError"
            class="border-danger-200 bg-danger-50 text-danger-800 dark:border-danger-900 dark:bg-danger-900/25 dark:text-danger-200 rounded-xl border px-4 py-3 text-sm font-medium"
            role="alert"
        >
            {{ flowError }}
        </p>

        <div
            class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start"
        >
            <div class="min-w-0">
                <section
                    v-if="currentStep === 1"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('reservations.form.dates.title') }}
                        </h2>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('reservations.form.dates.description') }}
                        </p>
                    </div>
                    <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
                        <div class="grid gap-2">
                            <InputLabel
                                for="planned-check-in"
                                :value="t('reservations.fields.check_in')"
                                required
                            />
                            <TextInput
                                id="planned-check-in"
                                v-model="form.planned_check_in_on"
                                type="date"
                            />
                            <InputError
                                :message="form.errors.planned_check_in_on"
                            />
                        </div>
                        <div class="grid gap-2">
                            <InputLabel
                                for="planned-check-out"
                                :value="t('reservations.fields.check_out')"
                                required
                            />
                            <TextInput
                                id="planned-check-out"
                                v-model="form.planned_check_out_on"
                                type="date"
                            />
                            <InputError
                                :message="form.errors.planned_check_out_on"
                            />
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="currentStep === 2"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('reservations.form.guests.title') }}
                        </h2>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('reservations.form.guests.description') }}
                        </p>
                    </div>
                    <div class="grid gap-5 p-5 sm:p-7">
                        <article
                            v-for="(entry, index) in form.guests"
                            :key="entry.key"
                            class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-800"
                        >
                            <div
                                class="mb-5 flex items-center justify-between gap-3"
                            >
                                <h3
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{
                                        entry.key === form.responsible_guest_key
                                            ? t(
                                                  'reservations.form.guests.responsible',
                                              )
                                            : `${t('reservations.form.guests.companion')} ${index}`
                                    }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <button
                                        v-if="entry.guest_id"
                                        type="button"
                                        class="text-primary-700 dark:text-primary-300 text-sm font-semibold"
                                        @click="registerNew(entry)"
                                    >
                                        {{ t('reservations.form.guests.new') }}
                                    </button>
                                    <button
                                        v-if="
                                            entry.key !==
                                            form.responsible_guest_key
                                        "
                                        type="button"
                                        class="text-danger-700 dark:text-danger-300 inline-flex h-11 w-11 items-center justify-center rounded-lg"
                                        :aria-label="t('app.remove')"
                                        @click="removeGuest(index)"
                                    >
                                        <XMarkIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                            <GuestLookupField
                                v-if="!entry.guest_id"
                                class="mb-5"
                                :hotel="hotel"
                                :id="`reservation-search-${entry.key}`"
                                @select="selectGuest(entry, $event)"
                            />
                            <GuestFields
                                :guest="entry"
                                :identification-types="identificationTypes"
                                :errors="form.errors"
                                :prefix="`guests.${index}`"
                                :id-prefix="`reservation-${entry.key}`"
                                :disabled="Boolean(entry.guest_id)"
                                @update:guest="Object.assign(entry, $event)"
                            />
                        </article>
                        <SecondaryButton
                            type="button"
                            class="justify-center"
                            @click="addCompanion"
                            ><UserPlusIcon class="h-4 w-4" />{{
                                t('reservations.form.guests.add')
                            }}</SecondaryButton
                        >
                    </div>
                </section>

                <section
                    v-else-if="currentStep === 3"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('reservations.form.rooms.title') }}
                        </h2>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('reservations.form.rooms.description') }}
                        </p>
                    </div>
                    <div class="grid gap-7 p-5 sm:p-7">
                        <p
                            v-if="loadingRooms"
                            class="rounded-xl bg-neutral-100 px-4 py-5 text-sm text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                        >
                            {{ t('reservations.form.rooms.loading') }}
                        </p>
                        <div
                            v-else-if="rooms.length"
                            class="grid gap-3 md:grid-cols-2"
                        >
                            <button
                                v-for="room in rooms"
                                :key="room.id"
                                type="button"
                                class="focus-visible:ring-primary-500 relative min-h-36 rounded-xl border p-4 text-start transition-colors focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none"
                                :class="
                                    selectedRoomIds.includes(Number(room.id))
                                        ? 'border-primary-600 bg-primary-50 dark:border-primary-400 dark:bg-primary-950/40'
                                        : 'border-neutral-200 hover:border-neutral-400 dark:border-neutral-800 dark:hover:border-neutral-600'
                                "
                                :aria-pressed="
                                    selectedRoomIds.includes(Number(room.id))
                                "
                                @click="toggleRoom(room)"
                            >
                                <BuildingOffice2Icon
                                    class="h-6 w-6 text-neutral-500"
                                />
                                <span
                                    class="mt-4 block font-semibold text-neutral-950 dark:text-white"
                                    >{{
                                        t('reservations.form.rooms.number', {
                                            number: room.number,
                                        })
                                    }}</span
                                >
                                <span
                                    class="mt-1 block text-sm text-neutral-600 dark:text-neutral-400"
                                    >{{ room.room_type.name }} ·
                                    {{
                                        t('reservations.form.rooms.capacity', {
                                            count: room.room_type.capacity,
                                        })
                                    }}</span
                                >
                                <span
                                    class="mt-3 block text-sm font-semibold text-neutral-950 tabular-nums dark:text-white"
                                    >{{ formatMoney(room.reference_price) }} /
                                    {{
                                        t('reservations.form.rooms.night')
                                    }}</span
                                >
                                <CheckCircleIcon
                                    v-if="
                                        selectedRoomIds.includes(
                                            Number(room.id),
                                        )
                                    "
                                    class="text-primary-700 dark:text-primary-300 absolute end-4 top-4 h-5 w-5"
                                />
                            </button>
                        </div>
                        <p
                            v-else
                            class="rounded-xl bg-neutral-100 px-4 py-5 text-sm text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                        >
                            {{ t('reservations.form.rooms.empty') }}
                        </p>

                        <div
                            v-if="selectedRooms.length"
                            class="grid gap-4 border-t border-neutral-200 pt-7 dark:border-neutral-800"
                        >
                            <article
                                v-for="(
                                    { room, selection }, index
                                ) in selectedRooms"
                                :key="room.id"
                                class="grid gap-4 rounded-xl border border-neutral-200 p-4 sm:grid-cols-[minmax(0,1fr)_14rem] dark:border-neutral-800"
                            >
                                <div>
                                    <p
                                        class="font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{
                                            t(
                                                'reservations.form.rooms.number',
                                                { number: room.number },
                                            )
                                        }}
                                    </p>
                                    <p
                                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{ guestCount(room.id) }}/{{
                                            room.room_type.capacity
                                        }}
                                        {{
                                            t(
                                                'reservations.form.rooms.assigned',
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="grid gap-2">
                                    <InputLabel
                                        :for="`rate-${index}`"
                                        :value="t('reservations.fields.rate')"
                                        required
                                    />
                                    <TextInput
                                        :id="`rate-${index}`"
                                        v-model="selection.nightly_rate"
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        class="w-full tabular-nums"
                                    />
                                </div>
                            </article>
                        </div>

                        <div
                            v-if="selectedRooms.length"
                            class="grid gap-3 border-t border-neutral-200 pt-7 dark:border-neutral-800"
                        >
                            <h3
                                class="font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('reservations.form.rooms.assign') }}
                            </h3>
                            <article
                                v-for="guest in form.guests"
                                :key="guest.key"
                                class="grid gap-3 rounded-xl border border-neutral-200 p-4 sm:grid-cols-[minmax(0,1fr)_16rem] sm:items-center dark:border-neutral-800"
                            >
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ guestName(guest) }}
                                </p>
                                <select
                                    :value="assignedRoomId(guest)"
                                    class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                    @change="
                                        assignGuest(guest, $event.target.value)
                                    "
                                >
                                    <option value="">
                                        {{
                                            t('reservations.form.rooms.choose')
                                        }}
                                    </option>
                                    <option
                                        v-for="{ room } in selectedRooms"
                                        :key="room.id"
                                        :value="room.id"
                                        :disabled="
                                            assignedRoomId(guest) !== room.id &&
                                            guestCount(room.id) >=
                                                room.room_type.capacity
                                        "
                                    >
                                        {{ room.number }} ·
                                        {{ guestCount(room.id) }}/{{
                                            room.room_type.capacity
                                        }}
                                    </option>
                                </select>
                            </article>
                            <InputError :message="form.errors.reserved_rooms" />
                        </div>
                    </div>
                </section>

                <section
                    v-else
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('reservations.form.review.title') }}
                        </h2>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('reservations.form.review.description') }}
                        </p>
                    </div>
                    <div class="grid gap-7 p-5 sm:p-7">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div
                                v-for="guest in form.guests"
                                :key="guest.key"
                                class="rounded-xl border border-neutral-200 px-4 py-3 dark:border-neutral-800"
                            >
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ guestName(guest) }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        rooms.find(
                                            (room) =>
                                                Number(room.id) ===
                                                Number(assignedRoomId(guest)),
                                        )?.number
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="border-t border-neutral-200 pt-6 dark:border-neutral-800"
                        >
                            <p
                                class="text-sm text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('reservations.form.review.draft_note') }}
                            </p>
                        </div>
                    </div>
                </section>

                <footer
                    class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <SecondaryButton
                        v-if="currentStep > 1"
                        type="button"
                        @click="previousStep"
                        ><ChevronLeftIcon class="h-4 w-4" />{{
                            t('reservations.actions.back')
                        }}</SecondaryButton
                    ><span v-else />
                    <PrimaryButton
                        v-if="currentStep < 4"
                        type="button"
                        @click="nextStep"
                        >{{ t('reservations.actions.continue')
                        }}<ChevronRightIcon class="h-4 w-4"
                    /></PrimaryButton>
                    <PrimaryButton
                        v-else
                        type="submit"
                        :disabled="form.processing"
                        ><CheckIcon class="h-4 w-4" />{{
                            form.processing
                                ? t('app.saving')
                                : t('reservations.actions.save_draft')
                        }}</PrimaryButton
                    >
                </footer>
            </div>

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
                                {{ form.planned_check_in_on }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-neutral-300 dark:text-neutral-600">
                                {{ t('reservations.fields.check_out') }}
                            </dt>
                            <dd class="mt-1 font-semibold tabular-nums">
                                {{ form.planned_check_out_on }}
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
                                {{ form.guests.length }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-neutral-300 dark:text-neutral-600">
                                {{ t('reservations.fields.rooms') }}
                            </dt>
                            <dd class="mt-1 font-semibold tabular-nums">
                                {{ form.reserved_rooms.length }}
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
        </div>
    </form>
</template>
