<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowPathIcon,
    ArrowsRightLeftIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    stay: Object,
    rooms: Array,
    currency: String,
});
const { t } = useI18n();
const isTransferOpen = ref(false);
const selectedOccupancy = ref(null);
const expectedCheckOutForm = useForm({
    expected_check_out_on: props.stay.expected_check_out_on,
});
const checkOutForm = useForm({});
const transferForm = useForm({ room_id: '', nightly_rate: '' });
const isActive = computed(() => props.stay.status === 'active');

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
                                    {{ occupancy.nightly_rate }}
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
                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('stays.pages.show.group') }}
                        </h2>
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
