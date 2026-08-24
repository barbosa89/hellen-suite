<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import DangerButton from '@/Components/DangerButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import RoomModuleTabs from '@/Components/RoomModuleTabs.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    BuildingOffice2Icon,
    PencilSquareIcon,
    PlusIcon,
    RectangleGroupIcon,
    TrashIcon,
    WrenchScrewdriverIcon,
} from '@heroicons/vue/24/outline';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: { type: Object, required: true },
    rooms: { type: Object, required: true },
    currency: { type: String, default: null },
    hasRoomTypes: { type: Boolean, required: true },
    flash: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();
const selectedRoom = ref(null);
const deleting = ref(false);
const isCurrencyConfigured = computed(() => props.currency !== null);

function formatPrice(value) {
    if (!props.currency) {
        return value;
    }

    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: props.currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value));
}

function setHousekeepingStatus(room, housekeepingStatus) {
    router.patch(
        route('hotels.rooms.housekeeping-status.update', [
            props.hotel.id,
            room.id,
        ]),
        { housekeeping_status: housekeepingStatus },
        { preserveScroll: true },
    );
}

function toggleActivity(room) {
    router.patch(
        route('hotels.rooms.toggle', [props.hotel.id, room.id]),
        {},
        { preserveScroll: true },
    );
}

function requestDeletion(room) {
    selectedRoom.value = room;
}

function closeDeletion() {
    if (!deleting.value) {
        selectedRoom.value = null;
    }
}

function destroy() {
    if (!selectedRoom.value) {
        return;
    }

    deleting.value = true;

    router.delete(
        route('hotels.rooms.destroy', [props.hotel.id, selectedRoom.value.id]),
        {
            preserveScroll: true,
            onFinish: () => {
                deleting.value = false;
                selectedRoom.value = null;
            },
        },
    );
}
</script>

<template>
    <Head
        :title="t('rooms.pages.index.heading', { hotel: hotel.business_name })"
    />

    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('rooms.title')"
                    :description="
                        t('rooms.pages.index.description', {
                            hotel: hotel.business_name,
                        })
                    "
                >
                    <template #actions>
                        <ActionLink
                            v-if="isCurrencyConfigured && hasRoomTypes"
                            :href="route('hotels.rooms.create', hotel.id)"
                        >
                            <PlusIcon class="h-4 w-4" />
                            {{ t('rooms.actions.create') }}
                        </ActionLink>
                        <ActionLink
                            v-else-if="!isCurrencyConfigured"
                            :href="route('settings.edit')"
                        >
                            <WrenchScrewdriverIcon class="h-4 w-4" />
                            {{ t('settings.actions.configure_currency') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <RoomModuleTabs :hotel="hotel" active="rooms" />

                <FlashMessage v-if="flash.success" :message="flash.success" />
                <FlashMessage
                    v-if="flash.error"
                    :message="flash.error"
                    variant="error"
                />

                <section
                    v-if="!isCurrencyConfigured"
                    class="flex min-h-80 flex-col items-center justify-center rounded-2xl bg-white px-6 py-12 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-secondary-50 text-secondary-800 dark:bg-secondary-900/30 dark:text-secondary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                    >
                        <WrenchScrewdriverIcon class="h-7 w-7" />
                    </span>
                    <h2
                        class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('rooms.pages.index.currency_required_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('rooms.pages.index.currency_required') }}
                    </p>
                    <ActionLink :href="route('settings.edit')" class="mt-6">
                        {{ t('settings.actions.configure_currency') }}
                    </ActionLink>
                </section>

                <section
                    v-else-if="rooms.data.length"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    :aria-label="t('rooms.pages.index.inventory_label')"
                >
                    <div
                        class="hidden min-h-11 grid-cols-[minmax(7rem,0.55fr)_minmax(12rem,1fr)_minmax(7rem,0.5fr)_minmax(10rem,0.8fr)_minmax(12rem,1fr)_auto] items-center gap-5 border-b border-neutral-200 bg-neutral-50 px-5 text-xs font-semibold text-neutral-600 lg:grid xl:px-6 dark:border-neutral-800 dark:bg-neutral-950/60 dark:text-neutral-400"
                    >
                        <span>{{ t('rooms.fields.number.label') }}</span>
                        <span>{{ t('rooms.fields.room_type.label') }}</span>
                        <span>{{ t('rooms.fields.floor.label') }}</span>
                        <span>{{
                            t('rooms.fields.reference_price.label')
                        }}</span>
                        <span>{{ t('rooms.fields.operation.label') }}</span>
                        <span class="sr-only">{{ t('app.actions') }}</span>
                    </div>

                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="room in rooms.data"
                            :key="room.id"
                            class="grid gap-5 px-5 py-5 lg:grid-cols-[minmax(7rem,0.55fr)_minmax(12rem,1fr)_minmax(7rem,0.5fr)_minmax(10rem,0.8fr)_minmax(12rem,1fr)_auto] lg:items-center xl:px-6"
                        >
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('rooms.fields.number.label') }}
                                </p>
                                <p
                                    class="text-base font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ room.number }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('rooms.fields.room_type.label') }}
                                </p>
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ room.room_type.name }}
                                </p>
                                <p
                                    class="mt-0.5 text-xs text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        t('room_types.capacity', {
                                            count: room.room_type.capacity,
                                        })
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('rooms.fields.floor.label') }}
                                </p>
                                <p
                                    class="text-sm text-neutral-700 tabular-nums dark:text-neutral-300"
                                >
                                    {{ room.floor || t('app.not_provided') }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{
                                        t('rooms.fields.reference_price.label')
                                    }}
                                </p>
                                <p
                                    class="text-sm font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ formatPrice(room.reference_price) }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('rooms.fields.operation.label') }}
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="focus-visible:ring-primary-500 inline-flex min-h-9 items-center rounded-lg border px-3 py-1.5 text-xs font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden dark:focus-visible:ring-offset-neutral-950"
                                        :class="
                                            room.housekeeping_status === 'clean'
                                                ? 'border-success-200 bg-success-50 text-success-800 dark:border-success-900 dark:bg-success-900/25 dark:text-success-300'
                                                : 'hover:border-success-300 hover:bg-success-50 dark:hover:border-success-800 dark:hover:bg-success-900/25 border-neutral-300 bg-white text-neutral-700 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200'
                                        "
                                        :aria-pressed="
                                            room.housekeeping_status === 'clean'
                                        "
                                        @click="
                                            setHousekeepingStatus(room, 'clean')
                                        "
                                    >
                                        {{ t('rooms.housekeeping.clean') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="focus-visible:ring-primary-500 inline-flex min-h-9 items-center rounded-lg border px-3 py-1.5 text-xs font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden dark:focus-visible:ring-offset-neutral-950"
                                        :class="
                                            room.housekeeping_status === 'dirty'
                                                ? 'border-secondary-200 bg-secondary-50 text-secondary-800 dark:border-secondary-800 dark:bg-secondary-900/30 dark:text-secondary-300'
                                                : 'hover:border-secondary-300 hover:bg-secondary-50 dark:hover:border-secondary-800 dark:hover:bg-secondary-900/30 border-neutral-300 bg-white text-neutral-700 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200'
                                        "
                                        :aria-pressed="
                                            room.housekeeping_status === 'dirty'
                                        "
                                        @click="
                                            setHousekeepingStatus(room, 'dirty')
                                        "
                                    >
                                        {{ t('rooms.housekeeping.dirty') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="focus-visible:ring-primary-500 inline-flex min-h-9 items-center rounded-lg border px-3 py-1.5 text-xs font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden dark:focus-visible:ring-offset-neutral-950"
                                        :class="
                                            room.is_active
                                                ? 'border-primary-200 bg-primary-50 text-primary-800 dark:border-primary-900 dark:bg-primary-950 dark:text-primary-300'
                                                : 'border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800'
                                        "
                                        :aria-pressed="room.is_active"
                                        @click="toggleActivity(room)"
                                    >
                                        {{
                                            room.is_active
                                                ? t('rooms.activity.active')
                                                : t('rooms.activity.inactive')
                                        }}
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:justify-end">
                                <ActionLink
                                    :href="
                                        route('hotels.rooms.edit', [
                                            hotel.id,
                                            room.id,
                                        ])
                                    "
                                    variant="secondary"
                                    size="sm"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                    <span class="lg:sr-only">{{
                                        t('rooms.actions.edit')
                                    }}</span>
                                </ActionLink>
                                <button
                                    type="button"
                                    class="focus-visible:ring-danger-500 border-danger-200 text-danger-700 hover:border-danger-300 hover:bg-danger-50 dark:border-danger-900 dark:text-danger-300 dark:hover:bg-danger-900/30 inline-flex min-h-11 items-center justify-center rounded-lg border bg-white px-3 py-1.5 shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden sm:min-h-9 dark:bg-neutral-900 dark:focus-visible:ring-offset-neutral-950"
                                    :aria-label="t('rooms.actions.delete')"
                                    @click="requestDeletion(room)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </li>
                    </ul>
                    <Pagination :pagination="rooms" />
                </section>

                <section
                    v-else
                    class="flex min-h-80 flex-col items-center justify-center rounded-2xl bg-white px-6 py-12 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                        ><RectangleGroupIcon class="h-7 w-7"
                    /></span>
                    <h2
                        class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('rooms.pages.index.empty_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('rooms.pages.index.empty') }}
                    </p>
                    <ActionLink
                        :href="
                            hasRoomTypes
                                ? route('hotels.rooms.create', hotel.id)
                                : route('hotels.room-types.create', hotel.id)
                        "
                        class="mt-6"
                        ><BuildingOffice2Icon class="h-4 w-4" />{{
                            hasRoomTypes
                                ? t('rooms.actions.create')
                                : t('room_types.actions.create')
                        }}</ActionLink
                    >
                </section>
            </div>
        </div>

        <Modal
            :show="selectedRoom !== null"
            max-width="md"
            aria-labelledby="delete-room-title"
            @close="closeDeletion"
        >
            <div class="p-6 sm:p-7">
                <span
                    class="bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300 flex h-11 w-11 items-center justify-center rounded-xl"
                    ><TrashIcon class="h-5 w-5"
                /></span>
                <h2
                    id="delete-room-title"
                    class="mt-5 text-xl font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white"
                >
                    {{ t('rooms.pages.index.delete_title') }}
                </h2>
                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{
                        selectedRoom
                            ? t('rooms.pages.index.delete_confirm', {
                                  number: selectedRoom.number,
                              })
                            : ''
                    }}
                </p>
                <div
                    class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <SecondaryButton
                        :disabled="deleting"
                        @click="closeDeletion"
                        >{{ t('app.cancel') }}</SecondaryButton
                    >
                    <DangerButton :disabled="deleting" @click="destroy"
                        ><TrashIcon class="h-4 w-4" />{{
                            deleting
                                ? t('app.deleting')
                                : t('rooms.actions.delete')
                        }}</DangerButton
                    >
                </div>
            </div>
        </Modal>
    </DefaultLayout>
</template>
