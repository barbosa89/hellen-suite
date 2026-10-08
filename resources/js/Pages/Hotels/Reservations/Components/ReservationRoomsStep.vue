<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ReservationStepPanel from '@/Pages/Hotels/Reservations/Components/ReservationStepPanel.vue';
import {
    BuildingOffice2Icon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    rooms: { type: Array, required: true },
    reservedRooms: { type: Array, required: true },
    guests: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
    loading: { type: Boolean, default: false },
    currency: { type: String, required: true },
    traEnabled: { type: Boolean, default: false },
});

const emit = defineEmits([
    'toggle-room',
    'update-room-rate',
    'assign-guest',
    'select-principal',
]);
const { t, locale } = useI18n();

const selectedRoomIds = computed(() =>
    props.reservedRooms.map((item) => Number(item.room_id)),
);
const selectedRooms = computed(() =>
    props.reservedRooms
        .map((selection) => ({
            selection,
            room: props.rooms.find(
                (room) => Number(room.id) === Number(selection.room_id),
            ),
        }))
        .filter((item) => item.room),
);

function roomSelection(roomId) {
    return props.reservedRooms.find(
        (item) => Number(item.room_id) === Number(roomId),
    );
}

function assignedRoomId(guest) {
    return (
        props.reservedRooms.find((room) => room.guest_keys.includes(guest.key))
            ?.room_id ?? ''
    );
}

function guestName(guest) {
    return (
        `${guest.first_name} ${guest.last_name}`.trim() ||
        t('reservations.form.guests.unnamed')
    );
}

function guestNameFor(guestKey) {
    const guest = props.guests.find((entry) => entry.key === guestKey);

    if (!guest) {
        return guestKey;
    }

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
</script>

<template>
    <ReservationStepPanel
        :title="t('reservations.form.rooms.title')"
        :description="t('reservations.form.rooms.description')"
    >
        <div class="grid gap-7 p-5 sm:p-7">
            <p
                v-if="loading"
                class="rounded-xl bg-neutral-100 px-4 py-5 text-sm text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
            >
                {{ t('reservations.form.rooms.loading') }}
            </p>
            <div v-else-if="rooms.length" class="grid gap-3 md:grid-cols-2">
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
                    :aria-pressed="selectedRoomIds.includes(Number(room.id))"
                    @click="emit('toggle-room', room)"
                >
                    <BuildingOffice2Icon class="h-6 w-6 text-neutral-500" />
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
                        {{ t('reservations.form.rooms.night') }}</span
                    >
                    <CheckCircleIcon
                        v-if="selectedRoomIds.includes(Number(room.id))"
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
                    v-for="({ room, selection }, index) in selectedRooms"
                    :key="room.id"
                    class="grid gap-4 rounded-xl border border-neutral-200 p-4 sm:grid-cols-[minmax(0,1fr)_14rem] dark:border-neutral-800"
                >
                    <div>
                        <p
                            class="font-semibold text-neutral-950 dark:text-white"
                        >
                            {{
                                t('reservations.form.rooms.number', {
                                    number: room.number,
                                })
                            }}
                        </p>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ guestCount(room.id) }}/{{
                                room.room_type.capacity
                            }}
                            {{ t('reservations.form.rooms.assigned') }}
                        </p>
                        <fieldset v-if="traEnabled" class="mt-3 grid gap-2">
                            <legend
                                class="text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                            >
                                {{
                                    t('reservations.form.rooms.principal_label')
                                }}
                            </legend>
                            <label
                                v-for="guestKey in selection.guest_keys"
                                :key="guestKey"
                                class="flex cursor-pointer items-center gap-2 text-sm text-neutral-950 dark:text-neutral-100"
                            >
                                <input
                                    type="radio"
                                    :name="`principal-${index}`"
                                    :value="guestKey"
                                    :checked="
                                        selection.principal_guest_key ===
                                        guestKey
                                    "
                                    class="text-primary-600 focus:ring-primary-500 h-4 w-4 border-neutral-300"
                                    @change="
                                        emit(
                                            'select-principal',
                                            room.id,
                                            guestKey,
                                        )
                                    "
                                />
                                {{ guestNameFor(guestKey) }}
                            </label>
                            <InputError
                                :message="
                                    errors[
                                        `reserved_rooms.${index}.principal_guest_key`
                                    ]
                                "
                            />
                        </fieldset>
                    </div>
                    <div class="grid gap-2">
                        <InputLabel
                            :for="`rate-${index}`"
                            :value="t('reservations.fields.rate')"
                            required
                        />
                        <TextInput
                            :id="`rate-${index}`"
                            :model-value="String(selection.nightly_rate)"
                            type="number"
                            min="0.01"
                            step="0.01"
                            class="w-full tabular-nums"
                            @update:model-value="
                                emit('update-room-rate', room.id, $event)
                            "
                        />
                    </div>
                </article>
            </div>

            <div
                v-if="selectedRooms.length"
                class="grid gap-3 border-t border-neutral-200 pt-7 dark:border-neutral-800"
            >
                <h3 class="font-semibold text-neutral-950 dark:text-white">
                    {{ t('reservations.form.rooms.assign') }}
                </h3>
                <article
                    v-for="guest in guests"
                    :key="guest.key"
                    class="grid gap-3 rounded-xl border border-neutral-200 p-4 sm:grid-cols-[minmax(0,1fr)_16rem] sm:items-center dark:border-neutral-800"
                >
                    <p class="font-semibold text-neutral-950 dark:text-white">
                        {{ guestName(guest) }}
                    </p>
                    <select
                        :value="assignedRoomId(guest)"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                        @change="
                            emit('assign-guest', guest.key, $event.target.value)
                        "
                    >
                        <option value="">
                            {{ t('reservations.form.rooms.choose') }}
                        </option>
                        <option
                            v-for="{ room } in selectedRooms"
                            :key="room.id"
                            :value="room.id"
                            :disabled="
                                Number(assignedRoomId(guest)) !==
                                    Number(room.id) &&
                                guestCount(room.id) >= room.room_type.capacity
                            "
                        >
                            {{ room.number }} · {{ guestCount(room.id) }}/{{
                                room.room_type.capacity
                            }}
                        </option>
                    </select>
                </article>
                <InputError :message="errors.reserved_rooms" />
            </div>
        </div>
    </ReservationStepPanel>
</template>
