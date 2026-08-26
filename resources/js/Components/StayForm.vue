<script setup>
import GuestFields from '@/Components/GuestFields.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {
    BuildingOffice2Icon,
    CalendarDaysIcon,
    CheckCircleIcon,
    CheckIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    UserPlusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    rooms: Array,
    identificationTypes: Array,
    currency: String,
});
const { t } = useI18n();
const currentStep = ref(1);
const flowError = ref('');
const searchTerms = reactive({});
const searchResults = reactive({});
const searching = reactive({});
const searchTimers = {};

const steps = computed(() => [
    { number: 1, label: t('stays.form.steps.guests') },
    { number: 2, label: t('stays.form.steps.rooms') },
    { number: 3, label: t('stays.form.steps.review') },
]);

function guest(
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

const form = useForm({
    expected_check_out_on: new Date(Date.now() + 86400000)
        .toISOString()
        .slice(0, 10),
    responsible_guest_key: 'responsible',
    guests: [guest('responsible')],
    room_occupancies: [],
});

const selectedRoomIds = computed(() =>
    form.room_occupancies.map((occupancy) => Number(occupancy.room_id)),
);
const selectedRooms = computed(() =>
    form.room_occupancies
        .map((occupancy) => ({ occupancy, room: roomForId(occupancy.room_id) }))
        .filter(({ room }) => room),
);

function guestName(entry, index) {
    const name = `${entry.first_name} ${entry.last_name}`.trim();
    return name || `${t('stays.form.guests.companion')} ${index + 1}`;
}
function roomForId(roomId) {
    return props.rooms.find((room) => Number(room.id) === Number(roomId));
}
function occupancyForRoom(roomId) {
    return form.room_occupancies.find(
        (occupancy) => Number(occupancy.room_id) === Number(roomId),
    );
}
function isRoomSelected(roomId) {
    return selectedRoomIds.value.includes(Number(roomId));
}
function roomGuestCount(roomId) {
    return occupancyForRoom(roomId)?.guest_keys.length ?? 0;
}
function assignedRoomId(entry) {
    return (
        form.room_occupancies.find((occupancy) =>
            occupancy.guest_keys.includes(entry.key),
        )?.room_id ?? ''
    );
}
function formatPrice(value) {
    return new Intl.NumberFormat(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(Number(value));
}

function addCompanion() {
    form.guests.push(guest());
}
function removeGuest(index) {
    const removed = form.guests[index];
    form.guests.splice(index, 1);
    form.room_occupancies.forEach((occupancy) => {
        occupancy.guest_keys = occupancy.guest_keys.filter(
            (key) => key !== removed.key,
        );
    });
}
function toggleRoom(room) {
    const occupancyIndex = form.room_occupancies.findIndex(
        (occupancy) => Number(occupancy.room_id) === Number(room.id),
    );
    if (occupancyIndex >= 0) {
        form.room_occupancies.splice(occupancyIndex, 1);
        return;
    }
    form.room_occupancies.push({
        room_id: room.id,
        nightly_rate: room.reference_price,
        guest_keys: [],
    });
}
function assignGuest(entry, roomId) {
    form.room_occupancies.forEach((occupancy) => {
        occupancy.guest_keys = occupancy.guest_keys.filter(
            (key) => key !== entry.key,
        );
    });
    const occupancy = occupancyForRoom(roomId);
    if (occupancy) {
        occupancy.guest_keys.push(entry.key);
    }
}
function roomCanReceiveGuest(room, entry) {
    return (
        assignedRoomId(entry) === room.id ||
        roomGuestCount(room.id) < room.room_type.capacity
    );
}

function searchGuests(entry) {
    clearTimeout(searchTimers[entry.key]);
    const term = searchTerms[entry.key]?.trim();
    if (!term || term.length < 2) {
        searchResults[entry.key] = [];
        return;
    }
    searchTimers[entry.key] = setTimeout(async () => {
        searching[entry.key] = true;
        try {
            const response = await fetch(
                `${route('hotels.guests.lookup', props.hotel.id)}?search=${encodeURIComponent(term)}`,
            );
            const data = response.ok ? await response.json() : { guests: [] };
            searchResults[entry.key] = data.guests ?? [];
        } finally {
            searching[entry.key] = false;
        }
    }, 250);
}
function selectGuest(entry, selectedGuest) {
    Object.assign(entry, {
        key: entry.key,
        guest_id: selectedGuest.id,
        first_name: selectedGuest.first_name,
        last_name: selectedGuest.last_name,
        identification_type_id: selectedGuest.identification_type_id,
        identification_number: selectedGuest.identification_number,
        mobile: selectedGuest.mobile ?? '',
        email: selectedGuest.email ?? '',
    });
    searchTerms[entry.key] = '';
    searchResults[entry.key] = [];
}
function registerNew(entry) {
    Object.assign(entry, guest(entry.key));
    searchResults[entry.key] = [];
    searchTerms[entry.key] = '';
}

function validateGuests() {
    const incomplete = form.guests.some(
        (entry) =>
            !entry.guest_id &&
            (!entry.first_name ||
                !entry.last_name ||
                !entry.identification_type_id ||
                !entry.identification_number),
    );
    if (incomplete) {
        flowError.value = t('stays.form.messages.complete_guests');
        return false;
    }
    return true;
}
function validateRooms() {
    const allGuestsAssigned = form.guests.every((entry) =>
        Boolean(assignedRoomId(entry)),
    );
    const overCapacity = selectedRooms.value.some(
        ({ room }) => roomGuestCount(room.id) > room.room_type.capacity,
    );
    if (!form.expected_check_out_on) {
        flowError.value = t('stays.form.messages.select_check_out');
        return false;
    }
    if (!selectedRooms.value.length) {
        flowError.value = t('stays.form.messages.select_room');
        return false;
    }
    if (!allGuestsAssigned || overCapacity) {
        flowError.value = t('stays.form.messages.assign_guests');
        return false;
    }
    return true;
}
function nextStep() {
    flowError.value = '';
    if (currentStep.value === 1 ? validateGuests() : validateRooms()) {
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
    if (!validateRooms()) {
        currentStep.value = 2;
        return;
    }
    form.post(route('hotels.stays.store', props.hotel.id), {
        onError: (errors) => {
            currentStep.value = Object.keys(errors).some(
                (key) =>
                    key.startsWith('room_occupancies') ||
                    key === 'expected_check_out_on',
            )
                ? 2
                : 1;
        },
    });
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <nav
            class="rounded-2xl border border-neutral-200 bg-white p-2 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
            :aria-label="t('stays.form.steps.label')"
        >
            <ol class="grid gap-1 sm:grid-cols-3">
                <li v-for="step in steps" :key="step.number">
                    <button
                        type="button"
                        class="focus-visible:ring-primary-500 flex min-h-12 w-full items-center gap-3 rounded-xl px-3 text-start text-sm font-semibold focus-visible:ring-2 focus-visible:outline-hidden disabled:cursor-default"
                        :class="[
                            currentStep === step.number
                                ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                                : step.number < currentStep
                                  ? 'text-neutral-800 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800'
                                  : 'text-neutral-500 dark:text-neutral-500',
                        ]"
                        :disabled="step.number > currentStep"
                        :aria-current="
                            currentStep === step.number ? 'step' : undefined
                        "
                        @click="goToStep(step.number)"
                    >
                        <span
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs tabular-nums"
                            :class="
                                currentStep === step.number
                                    ? 'bg-primary-700 dark:bg-primary-400 text-white dark:text-neutral-950'
                                    : step.number < currentStep
                                      ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-950'
                                      : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400'
                            "
                            >{{ step.number }}</span
                        >{{ step.label }}
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
            class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] xl:items-start"
        >
            <div class="min-w-0">
                <section
                    v-if="currentStep === 1"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                ><UserGroupIcon class="h-5 w-5"
                            /></span>
                            <div>
                                <h2
                                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.form.guests.title') }}
                                </h2>
                                <p
                                    class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ t('stays.form.guests.description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-5 p-5 sm:p-7">
                        <article
                            v-for="(entry, index) in form.guests"
                            :key="entry.key"
                            class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-800"
                        >
                            <div
                                class="mb-5 flex flex-wrap items-center justify-between gap-3"
                            >
                                <div>
                                    <h3
                                        class="font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{
                                            entry.key ===
                                            form.responsible_guest_key
                                                ? t(
                                                      'stays.form.guests.responsible',
                                                  )
                                                : `${t('stays.form.guests.companion')} ${index}`
                                        }}
                                    </h3>
                                    <p
                                        v-if="entry.guest_id"
                                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{ guestName(entry, index) }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button
                                        v-if="entry.guest_id"
                                        type="button"
                                        class="text-primary-700 hover:text-primary-800 dark:text-primary-300 text-sm font-semibold"
                                        @click="registerNew(entry)"
                                    >
                                        {{
                                            t('stays.form.guests.new_guest')
                                        }}</button
                                    ><button
                                        v-if="
                                            entry.key !==
                                            form.responsible_guest_key
                                        "
                                        type="button"
                                        class="text-danger-700 hover:text-danger-800 dark:text-danger-300 inline-flex h-10 w-10 items-center justify-center rounded-lg"
                                        :aria-label="t('app.remove')"
                                        @click="removeGuest(index)"
                                    >
                                        <XMarkIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                            <div v-if="!entry.guest_id" class="relative mb-5">
                                <InputLabel
                                    :for="`guest-search-${entry.key}`"
                                    :value="t('stays.form.guests.search')"
                                />
                                <div class="relative mt-2">
                                    <MagnifyingGlassIcon
                                        class="absolute start-3 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-500"
                                    /><TextInput
                                        :id="`guest-search-${entry.key}`"
                                        v-model="searchTerms[entry.key]"
                                        class="w-full ps-10"
                                        autocomplete="off"
                                        @input="searchGuests(entry)"
                                    />
                                </div>
                                <p
                                    v-if="searching[entry.key]"
                                    class="mt-2 text-xs text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ t('app.searching') }}
                                </p>
                                <ul
                                    v-if="searchResults[entry.key]?.length"
                                    class="absolute z-10 mt-2 max-h-52 w-full overflow-y-auto rounded-xl border border-neutral-200 bg-white p-1 shadow-lg dark:border-neutral-700 dark:bg-neutral-900"
                                >
                                    <li
                                        v-for="result in searchResults[
                                            entry.key
                                        ]"
                                        :key="result.id"
                                    >
                                        <button
                                            type="button"
                                            class="focus-visible:ring-primary-500 w-full rounded-lg px-3 py-2 text-start hover:bg-neutral-100 focus-visible:ring-2 focus-visible:outline-hidden dark:hover:bg-neutral-800"
                                            @click="selectGuest(entry, result)"
                                        >
                                            <span
                                                class="block font-semibold text-neutral-950 dark:text-white"
                                                >{{ result.first_name }}
                                                {{ result.last_name }}</span
                                            ><span
                                                class="text-xs text-neutral-600 dark:text-neutral-400"
                                                >{{
                                                    result.identification_type
                                                        .code
                                                }}
                                                ·
                                                {{
                                                    result.identification_number
                                                }}</span
                                            >
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <GuestFields
                                :guest="entry"
                                :identification-types="identificationTypes"
                                :errors="form.errors"
                                :prefix="`guests.${index}`"
                                :id-prefix="`stay-${entry.key}`"
                                :disabled="Boolean(entry.guest_id)"
                                @update:guest="Object.assign(entry, $event)"
                            />
                        </article>
                        <SecondaryButton
                            type="button"
                            class="justify-center"
                            @click="addCompanion"
                            ><UserPlusIcon class="h-4 w-4" />{{
                                t('stays.form.guests.add_companion')
                            }}</SecondaryButton
                        >
                    </div>
                </section>

                <section
                    v-else-if="currentStep === 2"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                ><BuildingOffice2Icon class="h-5 w-5"
                            /></span>
                            <div>
                                <h2
                                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.form.rooms.title') }}
                                </h2>
                                <p
                                    class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ t('stays.form.rooms.description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-7 p-5 sm:p-7">
                        <div
                            class="grid gap-5 border-b border-neutral-200 pb-7 sm:grid-cols-2 dark:border-neutral-800"
                        >
                            <div class="grid gap-2">
                                <InputLabel
                                    for="expected-check-out-on"
                                    :value="
                                        t(
                                            'stays.fields.expected_check_out_on.label',
                                        )
                                    "
                                    required
                                /><TextInput
                                    id="expected-check-out-on"
                                    v-model="form.expected_check_out_on"
                                    type="date"
                                    class="w-full"
                                    :invalid="
                                        Boolean(
                                            form.errors.expected_check_out_on,
                                        )
                                    "
                                /><InputError
                                    :message="form.errors.expected_check_out_on"
                                />
                            </div>
                            <div class="grid content-end">
                                <p
                                    class="text-sm font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.form.stay.check_in_today') }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ t('stays.form.stay.check_in_hint') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div
                                class="flex flex-wrap items-end justify-between gap-3"
                            >
                                <div>
                                    <h3
                                        class="font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{
                                            t(
                                                'stays.form.rooms.available_title',
                                            )
                                        }}
                                    </h3>
                                    <p
                                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{
                                            t(
                                                'stays.form.rooms.available_description',
                                            )
                                        }}
                                    </p>
                                </div>
                                <p
                                    class="text-primary-700 dark:text-primary-300 text-sm font-semibold tabular-nums"
                                >
                                    {{
                                        t('stays.form.rooms.available_count', {
                                            count: rooms.length,
                                        })
                                    }}
                                </p>
                            </div>
                            <div
                                v-if="rooms.length"
                                class="mt-5 grid gap-3 md:grid-cols-2"
                            >
                                <button
                                    v-for="room in rooms"
                                    :key="room.id"
                                    type="button"
                                    class="focus-visible:ring-primary-500 relative min-h-36 rounded-xl border p-4 text-start transition-[border-color,background-color,box-shadow] duration-150 ease-out focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none"
                                    :class="
                                        isRoomSelected(room.id)
                                            ? 'border-primary-600 bg-primary-50 dark:border-primary-400 dark:bg-primary-950/40 shadow-sm'
                                            : 'border-neutral-200 bg-white hover:border-neutral-400 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-600'
                                    "
                                    :aria-pressed="isRoomSelected(room.id)"
                                    @click="toggleRoom(room)"
                                >
                                    <span
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                        :class="
                                            isRoomSelected(room.id)
                                                ? 'bg-primary-700 dark:bg-primary-400 text-white dark:text-neutral-950'
                                                : ''
                                        "
                                        ><BuildingOffice2Icon
                                            class="h-5 w-5" /></span
                                    ><span
                                        class="mt-4 block text-base font-semibold text-neutral-950 dark:text-white"
                                        >{{
                                            t('stays.form.rooms.room_number', {
                                                number: room.number,
                                            })
                                        }}</span
                                    ><span
                                        class="mt-1 block text-sm text-neutral-600 dark:text-neutral-400"
                                        >{{ room.room_type.name }} ·
                                        {{
                                            t('stays.form.rooms.capacity', {
                                                count: room.room_type.capacity,
                                            })
                                        }}</span
                                    ><span
                                        class="mt-3 block text-sm font-semibold text-neutral-950 tabular-nums dark:text-white"
                                        >{{ formatPrice(room.reference_price) }}
                                        {{ currency }}
                                        <span
                                            class="font-normal text-neutral-600 dark:text-neutral-400"
                                            >/
                                            {{
                                                t('stays.form.rooms.night')
                                            }}</span
                                        ></span
                                    ><CheckCircleIcon
                                        v-if="isRoomSelected(room.id)"
                                        class="text-primary-700 dark:text-primary-300 absolute end-4 top-4 h-5 w-5"
                                    />
                                </button>
                            </div>
                            <p
                                v-else
                                class="mt-5 rounded-xl bg-neutral-100 px-4 py-5 text-sm text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                            >
                                {{ t('stays.form.rooms.empty') }}
                            </p>
                        </div>
                        <div
                            v-if="selectedRooms.length"
                            class="grid gap-4 border-t border-neutral-200 pt-7 dark:border-neutral-800"
                        >
                            <div>
                                <h3
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.form.rooms.selected_title') }}
                                </h3>
                                <p
                                    class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        t(
                                            'stays.form.rooms.selected_description',
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="grid gap-3">
                                <article
                                    v-for="(
                                        { room, occupancy }, index
                                    ) in selectedRooms"
                                    :key="room.id"
                                    class="grid gap-4 rounded-xl border border-neutral-200 p-4 sm:grid-cols-[minmax(0,1fr)_13rem_auto] sm:items-end dark:border-neutral-800"
                                >
                                    <div>
                                        <p
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{
                                                t(
                                                    'stays.form.rooms.room_number',
                                                    { number: room.number },
                                                )
                                            }}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{ room.room_type.name }} ·
                                            {{ roomGuestCount(room.id) }}/{{
                                                room.room_type.capacity
                                            }}
                                            {{ t('stays.form.rooms.assigned') }}
                                        </p>
                                    </div>
                                    <div class="grid gap-2">
                                        <InputLabel
                                            :for="`rate-${index}`"
                                            :value="
                                                t(
                                                    'stays.fields.nightly_rate.label',
                                                )
                                            "
                                            required
                                        />
                                        <div class="relative">
                                            <TextInput
                                                :id="`rate-${index}`"
                                                v-model="occupancy.nightly_rate"
                                                type="number"
                                                min="0.01"
                                                step="0.01"
                                                class="w-full pe-16 tabular-nums"
                                                :invalid="
                                                    Boolean(
                                                        form.errors[
                                                            `room_occupancies.${index}.nightly_rate`
                                                        ],
                                                    )
                                                "
                                            /><span
                                                class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                                                >{{ currency }}</span
                                            >
                                        </div>
                                        <InputError
                                            :message="
                                                form.errors[
                                                    `room_occupancies.${index}.nightly_rate`
                                                ]
                                            "
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        class="text-danger-700 hover:bg-danger-50 dark:text-danger-300 dark:hover:bg-danger-900/30 inline-flex h-11 w-11 items-center justify-center rounded-lg"
                                        :aria-label="
                                            t('stays.form.rooms.remove_room', {
                                                number: room.number,
                                            })
                                        "
                                        @click="toggleRoom(room)"
                                    >
                                        <XMarkIcon class="h-5 w-5" />
                                    </button>
                                </article>
                            </div>
                        </div>
                        <div
                            v-if="selectedRooms.length"
                            class="grid gap-4 border-t border-neutral-200 pt-7 dark:border-neutral-800"
                        >
                            <div>
                                <h3
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.form.rooms.assign_title') }}
                                </h3>
                                <p
                                    class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        t('stays.form.rooms.assign_description')
                                    }}
                                </p>
                            </div>
                            <div class="grid gap-3">
                                <article
                                    v-for="(entry, index) in form.guests"
                                    :key="entry.key"
                                    class="grid gap-3 rounded-xl border border-neutral-200 p-4 sm:grid-cols-[minmax(0,1fr)_16rem] sm:items-center dark:border-neutral-800"
                                >
                                    <div>
                                        <p
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{ guestName(entry, index) }}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{
                                                entry.key ===
                                                form.responsible_guest_key
                                                    ? t(
                                                          'stays.form.guests.responsible',
                                                      )
                                                    : t(
                                                          'stays.form.guests.companion',
                                                      )
                                            }}
                                        </p>
                                    </div>
                                    <div class="grid gap-2">
                                        <InputLabel
                                            :for="`guest-room-${entry.key}`"
                                            :value="
                                                t(
                                                    'stays.form.rooms.assign_label',
                                                )
                                            "
                                        /><select
                                            :id="`guest-room-${entry.key}`"
                                            :value="assignedRoomId(entry)"
                                            class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                            @change="
                                                assignGuest(
                                                    entry,
                                                    $event.target.value,
                                                )
                                            "
                                        >
                                            <option value="">
                                                {{
                                                    t(
                                                        'stays.form.rooms.assign_placeholder',
                                                    )
                                                }}
                                            </option>
                                            <option
                                                v-for="{
                                                    room,
                                                } in selectedRooms"
                                                :key="room.id"
                                                :value="room.id"
                                                :disabled="
                                                    !roomCanReceiveGuest(
                                                        room,
                                                        entry,
                                                    )
                                                "
                                            >
                                                {{
                                                    t(
                                                        'stays.form.rooms.room_number',
                                                        { number: room.number },
                                                    )
                                                }}
                                                ·
                                                {{ roomGuestCount(room.id) }}/{{
                                                    room.room_type.capacity
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                </article>
                            </div>
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
                        <div class="flex items-start gap-3">
                            <span
                                class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                ><CheckIcon class="h-5 w-5"
                            /></span>
                            <div>
                                <h2
                                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ t('stays.form.review.title') }}
                                </h2>
                                <p
                                    class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ t('stays.form.review.description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-7 p-5 sm:p-7">
                        <div class="grid gap-3">
                            <h3
                                class="font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('stays.form.review.guests') }}
                            </h3>
                            <ul class="grid gap-2 sm:grid-cols-2">
                                <li
                                    v-for="(entry, index) in form.guests"
                                    :key="entry.key"
                                    class="rounded-xl border border-neutral-200 px-4 py-3 text-sm dark:border-neutral-800"
                                >
                                    <span
                                        class="font-semibold text-neutral-950 dark:text-white"
                                        >{{ guestName(entry, index) }}</span
                                    ><span
                                        class="mt-1 block text-neutral-600 dark:text-neutral-400"
                                        >{{
                                            roomForId(assignedRoomId(entry))
                                                ?.number
                                        }}
                                        ·
                                        {{
                                            entry.key ===
                                            form.responsible_guest_key
                                                ? t(
                                                      'stays.form.guests.responsible',
                                                  )
                                                : t(
                                                      'stays.form.guests.companion',
                                                  )
                                        }}</span
                                    >
                                </li>
                            </ul>
                        </div>
                        <div
                            class="grid gap-3 border-t border-neutral-200 pt-7 dark:border-neutral-800"
                        >
                            <h3
                                class="font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('stays.form.review.rooms') }}
                            </h3>
                            <ul class="grid gap-2">
                                <li
                                    v-for="{ room, occupancy } in selectedRooms"
                                    :key="room.id"
                                    class="flex items-center justify-between gap-4 rounded-xl border border-neutral-200 px-4 py-3 text-sm dark:border-neutral-800"
                                >
                                    <div>
                                        <span
                                            class="font-semibold text-neutral-950 dark:text-white"
                                            >{{
                                                t(
                                                    'stays.form.rooms.room_number',
                                                    { number: room.number },
                                                )
                                            }}</span
                                        ><span
                                            class="mt-1 block text-neutral-600 dark:text-neutral-400"
                                            >{{
                                                occupancy.guest_keys.length
                                            }}/{{ room.room_type.capacity }}
                                            {{
                                                t('stays.form.rooms.assigned')
                                            }}</span
                                        >
                                    </div>
                                    <span
                                        class="font-semibold text-neutral-950 tabular-nums dark:text-white"
                                        >{{
                                            formatPrice(occupancy.nightly_rate)
                                        }}
                                        {{ currency }}</span
                                    >
                                </li>
                            </ul>
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
                            t('stays.form.actions.back')
                        }}</SecondaryButton
                    ><span v-else></span
                    ><PrimaryButton
                        v-if="currentStep < 3"
                        type="button"
                        @click="nextStep"
                        >{{ t('stays.form.actions.continue')
                        }}<ChevronRightIcon class="h-4 w-4" /></PrimaryButton
                    ><PrimaryButton
                        v-else
                        type="submit"
                        :disabled="form.processing"
                        ><CheckIcon class="h-4 w-4" />{{
                            form.processing
                                ? t('app.saving')
                                : t('stays.actions.check_in')
                        }}</PrimaryButton
                    >
                </footer>
            </div>

            <aside
                class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm xl:sticky xl:top-6 dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="flex items-center gap-3">
                    <CalendarDaysIcon
                        class="text-primary-700 dark:text-primary-300 h-5 w-5"
                    />
                    <h2 class="font-semibold text-neutral-950 dark:text-white">
                        {{ t('stays.form.summary.title') }}
                    </h2>
                </div>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div>
                        <dt class="text-neutral-600 dark:text-neutral-400">
                            {{ t('stays.fields.expected_check_out_on.label') }}
                        </dt>
                        <dd
                            class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                        >
                            {{
                                form.expected_check_out_on ||
                                t('app.not_provided')
                            }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-600 dark:text-neutral-400">
                            {{ t('stays.form.summary.guests') }}
                        </dt>
                        <dd
                            class="mt-1 font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ form.guests.length }}
                        </dd>
                    </div>
                    <div
                        class="border-t border-neutral-200 pt-4 dark:border-neutral-800"
                    >
                        <dt class="text-neutral-600 dark:text-neutral-400">
                            {{ t('stays.form.summary.rooms') }}
                        </dt>
                        <dd v-if="selectedRooms.length" class="mt-2 grid gap-2">
                            <span
                                v-for="{ room } in selectedRooms"
                                :key="room.id"
                                class="flex items-center justify-between gap-3 font-semibold text-neutral-950 dark:text-white"
                                ><span>{{
                                    t('stays.form.rooms.room_number', {
                                        number: room.number,
                                    })
                                }}</span
                                ><span
                                    class="text-neutral-600 tabular-nums dark:text-neutral-400"
                                    >{{ roomGuestCount(room.id) }}/{{
                                        room.room_type.capacity
                                    }}</span
                                ></span
                            >
                        </dd>
                        <dd
                            v-else
                            class="mt-1 text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('stays.form.summary.no_rooms') }}
                        </dd>
                    </div>
                </dl>
            </aside>
        </div>
    </form>
</template>
