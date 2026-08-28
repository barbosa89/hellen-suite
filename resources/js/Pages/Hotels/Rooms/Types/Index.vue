<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import DangerButton from '@/Components/DangerButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import RoomModuleTabs from '@/Pages/Hotels/Rooms/Components/RoomModuleTabs.vue';
import {
    PencilSquareIcon,
    PlusIcon,
    SquaresPlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: { type: Object, required: true },
    roomTypes: { type: Object, required: true },
    flash: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const selectedRoomType = ref(null);
const deleting = ref(false);

function requestDeletion(roomType) {
    selectedRoomType.value = roomType;
}

function closeDeletion() {
    if (!deleting.value) {
        selectedRoomType.value = null;
    }
}

function destroy() {
    if (!selectedRoomType.value) {
        return;
    }

    deleting.value = true;
    router.delete(
        route('hotels.room-types.destroy', [
            props.hotel.id,
            selectedRoomType.value.id,
        ]),
        {
            preserveScroll: true,
            onFinish: () => {
                deleting.value = false;
                selectedRoomType.value = null;
            },
        },
    );
}
</script>

<template>
    <Head
        :title="
            t('room_types.pages.index.heading', { hotel: hotel.business_name })
        "
    />

    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('room_types.title')"
                    :description="
                        t('room_types.pages.index.description', {
                            hotel: hotel.business_name,
                        })
                    "
                >
                    <template #actions>
                        <ActionLink
                            :href="route('hotels.room-types.create', hotel.id)"
                        >
                            <PlusIcon class="h-4 w-4" />
                            {{ t('room_types.actions.create') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <RoomModuleTabs :hotel="hotel" active="types" />
                <FlashMessage v-if="flash.success" :message="flash.success" />
                <FlashMessage
                    v-if="flash.error"
                    :message="flash.error"
                    variant="error"
                />

                <section
                    v-if="roomTypes.data.length"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="hidden min-h-11 grid-cols-[minmax(14rem,1fr)_minmax(10rem,0.6fr)_minmax(10rem,0.6fr)_auto] items-center gap-5 border-b border-neutral-200 bg-neutral-50 px-5 text-xs font-semibold text-neutral-600 lg:grid xl:px-6 dark:border-neutral-800 dark:bg-neutral-950/60 dark:text-neutral-400"
                    >
                        <span>{{ t('room_types.fields.name.label') }}</span>
                        <span>{{ t('room_types.fields.capacity.label') }}</span>
                        <span>{{
                            t('room_types.fields.rooms_count.label')
                        }}</span>
                        <span class="sr-only">{{ t('app.actions') }}</span>
                    </div>
                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="roomType in roomTypes.data"
                            :key="roomType.id"
                            class="grid gap-5 px-5 py-5 lg:grid-cols-[minmax(14rem,1fr)_minmax(10rem,0.6fr)_minmax(10rem,0.6fr)_auto] lg:items-center xl:px-6"
                        >
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('room_types.fields.name.label') }}
                                </p>
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{ roomType.name }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('room_types.fields.capacity.label') }}
                                </p>
                                <p
                                    class="text-sm text-neutral-700 tabular-nums dark:text-neutral-300"
                                >
                                    {{
                                        t('room_types.capacity', {
                                            count: roomType.capacity,
                                        })
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{
                                        t('room_types.fields.rooms_count.label')
                                    }}
                                </p>
                                <p
                                    class="text-sm text-neutral-700 tabular-nums dark:text-neutral-300"
                                >
                                    {{
                                        t('room_types.rooms_count', {
                                            count: roomType.rooms_count,
                                        })
                                    }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 lg:justify-end">
                                <ActionLink
                                    :href="
                                        route('hotels.room-types.edit', [
                                            hotel.id,
                                            roomType.id,
                                        ])
                                    "
                                    variant="secondary"
                                    size="sm"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                    <span class="lg:sr-only">{{
                                        t('room_types.actions.edit')
                                    }}</span>
                                </ActionLink>
                                <button
                                    type="button"
                                    :disabled="roomType.rooms_count > 0"
                                    class="focus-visible:ring-danger-500 border-danger-200 text-danger-700 hover:border-danger-300 hover:bg-danger-50 dark:border-danger-900 dark:text-danger-300 dark:hover:bg-danger-900/30 inline-flex min-h-11 items-center justify-center rounded-lg border bg-white px-3 py-1.5 shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden disabled:cursor-not-allowed disabled:opacity-50 sm:min-h-9 dark:bg-neutral-900 dark:focus-visible:ring-offset-neutral-950"
                                    :title="
                                        roomType.rooms_count > 0
                                            ? t(
                                                  'room_types.pages.index.delete_disabled',
                                              )
                                            : t('room_types.actions.delete')
                                    "
                                    :aria-label="t('room_types.actions.delete')"
                                    @click="requestDeletion(roomType)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </li>
                    </ul>
                    <Pagination :pagination="roomTypes" />
                </section>

                <section
                    v-else
                    class="flex min-h-80 flex-col items-center justify-center rounded-2xl bg-white px-6 py-12 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                        ><SquaresPlusIcon class="h-7 w-7"
                    /></span>
                    <h2
                        class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('room_types.pages.index.empty_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('room_types.pages.index.empty') }}
                    </p>
                    <ActionLink
                        :href="route('hotels.room-types.create', hotel.id)"
                        class="mt-6"
                        ><PlusIcon class="h-4 w-4" />{{
                            t('room_types.actions.create')
                        }}</ActionLink
                    >
                </section>
            </div>
        </div>

        <Modal
            :show="selectedRoomType !== null"
            max-width="md"
            aria-labelledby="delete-room-type-title"
            @close="closeDeletion"
        >
            <div class="p-6 sm:p-7">
                <span
                    class="bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300 flex h-11 w-11 items-center justify-center rounded-xl"
                    ><TrashIcon class="h-5 w-5"
                /></span>
                <h2
                    id="delete-room-type-title"
                    class="mt-5 text-xl font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white"
                >
                    {{ t('room_types.pages.index.delete_title') }}
                </h2>
                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{
                        selectedRoomType
                            ? t('room_types.pages.index.delete_confirm', {
                                  name: selectedRoomType.name,
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
                                : t('room_types.actions.delete')
                        }}</DangerButton
                    >
                </div>
            </div>
        </Modal>
    </DefaultLayout>
</template>
