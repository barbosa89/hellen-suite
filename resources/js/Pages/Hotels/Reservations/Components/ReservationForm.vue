<script setup>
import ReservationDatesStep from '@/Pages/Hotels/Reservations/Components/ReservationDatesStep.vue';
import ReservationFormActions from '@/Pages/Hotels/Reservations/Components/ReservationFormActions.vue';
import ReservationGuestsStep from '@/Pages/Hotels/Reservations/Components/ReservationGuestsStep.vue';
import ReservationReviewStep from '@/Pages/Hotels/Reservations/Components/ReservationReviewStep.vue';
import ReservationRoomsStep from '@/Pages/Hotels/Reservations/Components/ReservationRoomsStep.vue';
import ReservationStepNavigation from '@/Pages/Hotels/Reservations/Components/ReservationStepNavigation.vue';
import ReservationSummary from '@/Pages/Hotels/Reservations/Components/ReservationSummary.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: { type: Object, required: true },
    reservation: { type: Object, default: null },
    identificationTypes: { type: Array, required: true },
    currency: { type: String, required: true },
    countries: { type: Array, default: () => [] },
    subdivisions: { type: Array, default: () => [] },
});

const { t } = useI18n();

const traEnabled = computed(
    () =>
        props.hotel?.country_code === 'CO' &&
        Boolean(props.hotel?.current_compliance_profile?.enabled),
);
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
        second_first_name: '',
        last_name: '',
        second_last_name: '',
        identification_type_id: '',
        identification_number: '',
        birth_date: '',
        gender: '',
        nationality: '',
        residence_country: '',
        residence_subdivision: '',
        residence_locality: '',
        origin_country: '',
        origin_subdivision: '',
        origin_locality: '',
        destination_country: '',
        destination_subdivision: '',
        destination_locality: '',
        travel_purpose: '',
        transport_means: '',
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
        second_first_name: entry.guest.second_first_name ?? '',
        last_name: entry.guest.last_name,
        second_last_name: entry.guest.second_last_name ?? '',
        identification_type_id: entry.guest.identification_type_id,
        identification_number: entry.guest.identification_number,
        birth_date: entry.guest.birth_date ?? '',
        gender: entry.guest.gender ?? '',
        nationality: entry.guest.nationality ?? '',
        residence_country:
            entry.residence_country ?? entry.guest.residence_country ?? '',
        residence_subdivision: entry.residence_subdivision ?? '',
        residence_locality: entry.residence_locality ?? '',
        origin_country: entry.origin_country ?? '',
        origin_subdivision: entry.origin_subdivision ?? '',
        origin_locality: entry.origin_locality ?? '',
        destination_country: entry.destination_country ?? '',
        destination_subdivision: entry.destination_subdivision ?? '',
        destination_locality: entry.destination_locality ?? '',
        travel_purpose: entry.travel_purpose ?? '',
        transport_means: entry.transport_means ?? '',
        mobile: entry.guest.mobile ?? '',
        email: entry.guest.email ?? '',
    }));
}

function initialReservedRooms() {
    if (!props.reservation) {
        return [];
    }

    return props.reservation.reserved_rooms.map((reservedRoom) => ({
        room_id: reservedRoom.room_id,
        nightly_rate: reservedRoom.nightly_rate,
        guest_keys: reservedRoom.reservation_guests.map(
            (reservationGuest) => `reservation-guest-${reservationGuest.id}`,
        ),
        principal_guest_key: reservedRoom.principal_guest_id
            ? `reservation-guest-${props.reservation.reservation_guests.find((entry) => entry.guest_id === reservedRoom.principal_guest_id)?.id ?? ''}`
            : '',
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

async function loadRooms() {
    availabilityRequest?.abort();
    availabilityRequest = undefined;
    loadingRooms.value = false;

    if (!form.planned_check_in_on || !form.planned_check_out_on) {
        return;
    }
    if (form.planned_check_out_on <= form.planned_check_in_on) {
        return;
    }

    const request = new AbortController();
    availabilityRequest = request;
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
            { signal: request.signal },
        );
        const data = response.ok ? await response.json() : { rooms: [] };

        if (availabilityRequest !== request) {
            return;
        }

        rooms.value = data.rooms ?? [];
        const availableIds = rooms.value.map((room) => Number(room.id));
        form.reserved_rooms = form.reserved_rooms.filter((item) =>
            availableIds.includes(Number(item.room_id)),
        );
    } catch (error) {
        if (error.name !== 'AbortError' && availabilityRequest === request) {
            rooms.value = [];
        }
    } finally {
        if (availabilityRequest === request) {
            loadingRooms.value = false;
        }
    }
}

function addCompanion() {
    form.guests.push(blankGuest());
}

function removeGuest(index) {
    const [removed] = form.guests.splice(index, 1);

    form.reserved_rooms.forEach((room) => {
        room.guest_keys = room.guest_keys.filter((key) => key !== removed.key);

        if (room.principal_guest_key === removed.key) {
            room.principal_guest_key = '';
        }
    });
}

function selectGuest(index, guest) {
    const entry = form.guests[index];

    Object.assign(entry, {
        key: entry.key,
        guest_id: guest.id,
        first_name: guest.first_name,
        second_first_name: guest.second_first_name ?? '',
        last_name: guest.last_name,
        second_last_name: guest.second_last_name ?? '',
        identification_type_id: guest.identification_type_id,
        identification_number: guest.identification_number,
        birth_date: guest.birth_date ?? '',
        gender: guest.gender ?? '',
        nationality: guest.nationality ?? '',
        residence_country: guest.residence_country ?? '',
        mobile: guest.mobile ?? '',
        email: guest.email ?? '',
    });
}

function resetGuest(index) {
    Object.assign(form.guests[index], blankGuest(form.guests[index].key));
}

function updateGuest(index, guest) {
    Object.assign(form.guests[index], guest);
}

function toggleRoom(room) {
    const index = form.reserved_rooms.findIndex(
        (item) => Number(item.room_id) === Number(room.id),
    );

    if (index >= 0) {
        form.reserved_rooms.splice(index, 1);

        return;
    }

    form.reserved_rooms.push({
        room_id: room.id,
        nightly_rate: room.reference_price,
        guest_keys: [],
        principal_guest_key: '',
    });
}

function updateRoomRate(roomId, nightlyRate) {
    const selection = roomSelection(roomId);

    if (selection) {
        selection.nightly_rate = nightlyRate;
    }
}

function assignGuest(guestKey, roomId) {
    form.reserved_rooms.forEach((room) => {
        room.guest_keys = room.guest_keys.filter((key) => key !== guestKey);

        if (room.principal_guest_key === guestKey) {
            room.principal_guest_key = '';
        }
    });
    roomSelection(roomId)?.guest_keys.push(guestKey);
}

function selectPrincipal(roomId, guestKey) {
    const selection = roomSelection(roomId);

    if (selection) {
        selection.principal_guest_key = guestKey;
    }
}

function validateDates() {
    if (
        !form.planned_check_in_on ||
        !form.planned_check_out_on ||
        form.planned_check_out_on <= form.planned_check_in_on
    ) {
        flowError.value = t('reservations.form.errors.dates');

        return false;
    }

    return true;
}

function validateGuests() {
    const hasIncompleteGuest = form.guests.some(
        (guest) =>
            !guest.guest_id &&
            (!guest.first_name ||
                !guest.last_name ||
                !guest.identification_type_id ||
                !guest.identification_number),
    );

    if (hasIncompleteGuest) {
        flowError.value = t('reservations.form.errors.guests');

        return false;
    }

    return true;
}

function validateRooms() {
    const hasUnassignedGuest = form.guests.some(
        (guest) => !assignedRoomId(guest),
    );

    if (!form.reserved_rooms.length || hasUnassignedGuest) {
        flowError.value = t('reservations.form.errors.rooms');

        return false;
    }

    if (
        traEnabled.value &&
        form.reserved_rooms.some((room) => !room.principal_guest_key)
    ) {
        flowError.value = t('reservations.form.errors.principal');

        return false;
    }

    return true;
}

function validateStep() {
    flowError.value = '';

    return (
        {
            1: validateDates,
            2: validateGuests,
            3: validateRooms,
        }[currentStep.value]?.() ?? true
    );
}

function nextStep() {
    if (validateStep()) {
        currentStep.value += 1;
    }
}

function previousStep() {
    flowError.value = '';
    currentStep.value -= 1;
}

function goToStep(step) {
    if (step < currentStep.value) {
        flowError.value = '';
        currentStep.value = step;
    }
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

        return;
    }

    form.post(route('hotels.reservations.store', props.hotel.id), options);
}

watch(() => [form.planned_check_in_on, form.planned_check_out_on], loadRooms);
onMounted(loadRooms);
onUnmounted(() => availabilityRequest?.abort());
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <ReservationStepNavigation
            :steps="steps"
            :current-step="currentStep"
            @select="goToStep"
        />

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
                <ReservationDatesStep
                    v-if="currentStep === 1"
                    v-model:planned-check-in-on="form.planned_check_in_on"
                    v-model:planned-check-out-on="form.planned_check_out_on"
                    :errors="form.errors"
                />
                <ReservationGuestsStep
                    v-else-if="currentStep === 2"
                    :hotel="hotel"
                    :guests="form.guests"
                    :responsible-guest-key="form.responsible_guest_key"
                    :identification-types="identificationTypes"
                    :countries="countries"
                    :subdivisions="subdivisions"
                    :errors="form.errors"
                    @add-guest="addCompanion"
                    @remove-guest="removeGuest"
                    @select-guest="selectGuest"
                    @reset-guest="resetGuest"
                    @update-guest="updateGuest"
                />
                <ReservationRoomsStep
                    v-else-if="currentStep === 3"
                    :rooms="rooms"
                    :reserved-rooms="form.reserved_rooms"
                    :guests="form.guests"
                    :errors="form.errors"
                    :loading="loadingRooms"
                    :currency="currency"
                    :tra-enabled="traEnabled"
                    @toggle-room="toggleRoom"
                    @update-room-rate="updateRoomRate"
                    @assign-guest="assignGuest"
                    @select-principal="selectPrincipal"
                />
                <ReservationReviewStep
                    v-else
                    :guests="form.guests"
                    :rooms="rooms"
                    :reserved-rooms="form.reserved_rooms"
                />

                <ReservationFormActions
                    :current-step="currentStep"
                    :processing="form.processing"
                    @previous="previousStep"
                    @next="nextStep"
                />
            </div>

            <ReservationSummary
                :planned-check-in-on="form.planned_check_in_on"
                :planned-check-out-on="form.planned_check_out_on"
                :nights="nights"
                :guest-count="form.guests.length"
                :room-count="form.reserved_rooms.length"
                :quoted-total="quotedTotal"
                :currency="currency"
            />
        </div>
    </form>
</template>
