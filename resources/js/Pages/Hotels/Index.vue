<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import HotelLayout from '@/Layouts/HotelLayout.vue';
import HotelAvatar from '@/Pages/Hotels/Components/HotelAvatar.vue';
import {
    ArrowTopRightOnSquareIcon,
    BuildingOffice2Icon,
    CheckCircleIcon,
    EllipsisVerticalIcon,
    EnvelopeIcon,
    EyeIcon,
    MapPinIcon,
    PencilSquareIcon,
    PhoneIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotels: {
        type: Object,
        required: true,
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

const { t } = useI18n();
const selectedHotel = ref(null);
const deleting = ref(false);

function requestDeletion(hotel) {
    selectedHotel.value = hotel;
}

function closeDeletion() {
    if (!deleting.value) {
        selectedHotel.value = null;
    }
}

function destroy() {
    if (!selectedHotel.value) {
        return;
    }

    router.delete(route('hotels.destroy', selectedHotel.value.id), {
        preserveScroll: true,
        onStart: () => {
            deleting.value = true;
        },
        onSuccess: () => {
            selectedHotel.value = null;
        },
        onFinish: () => {
            deleting.value = false;
        },
    });
}
</script>

<template>
    <Head :title="t('hotels.title')" />

    <HotelLayout>
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('hotels.title')"
                    :description="t('hotels.pages.index.description')"
                >
                    <template #meta>
                        <div
                            class="flex items-center gap-2 text-sm font-medium text-neutral-600 dark:text-neutral-400"
                        >
                            <BuildingOffice2Icon
                                class="text-primary-600 dark:text-primary-400 h-5 w-5"
                            />
                            <span>
                                <strong
                                    class="font-semibold text-neutral-950 tabular-nums dark:text-white"
                                    >{{ hotels.total }}</strong
                                >
                                {{
                                    t(
                                        'hotels.pages.index.registered',
                                        hotels.total,
                                    )
                                }}
                            </span>
                        </div>
                    </template>

                    <template #actions>
                        <ActionLink :href="route('hotels.create')">
                            <PlusIcon class="h-4 w-4" />
                            {{ t('hotels.actions.create') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <div
                    v-if="props.flash.success"
                    class="bg-success-50 text-success-800 ring-success-200 dark:bg-success-900/25 dark:text-success-300 dark:ring-success-800 flex items-start gap-3 rounded-xl px-4 py-3 text-sm font-medium ring-1"
                    role="status"
                >
                    <CheckCircleIcon class="mt-0.5 h-5 w-5 shrink-0" />
                    {{ props.flash.success }}
                </div>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    :aria-label="t('hotels.pages.index.directory_label')"
                >
                    <div
                        v-if="hotels.data.length > 0"
                        class="hidden min-h-11 grid-cols-[minmax(13rem,1.25fr)_minmax(7rem,0.5fr)_minmax(12rem,1fr)_minmax(12rem,1fr)_auto] items-center gap-5 border-b border-neutral-200 bg-neutral-50 px-5 text-xs font-semibold text-neutral-600 lg:grid xl:px-6 2xl:grid-cols-[minmax(16rem,1.3fr)_minmax(9rem,0.5fr)_minmax(16rem,1fr)_minmax(18rem,1.1fr)_auto] dark:border-neutral-800 dark:bg-neutral-950/60 dark:text-neutral-400"
                    >
                        <span>{{
                            t('hotels.fields.business_name.label')
                        }}</span>
                        <span>{{ t('hotels.fields.tin.label') }}</span>
                        <span>{{ t('hotels.fields.address.label') }}</span>
                        <span>{{ t('hotels.pages.index.contact') }}</span>
                        <span class="text-end">{{ t('app.actions') }}</span>
                    </div>

                    <ul
                        v-if="hotels.data.length > 0"
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="hotel in hotels.data"
                            :key="hotel.id"
                            class="group before:bg-primary-500 relative grid gap-5 px-5 py-5 transition-colors duration-150 ease-out before:absolute before:inset-y-5 before:start-0 before:w-px hover:bg-neutral-50 motion-reduce:transition-none lg:grid-cols-[minmax(13rem,1.25fr)_minmax(7rem,0.5fr)_minmax(12rem,1fr)_minmax(12rem,1fr)_auto] lg:items-center xl:px-6 2xl:grid-cols-[minmax(16rem,1.3fr)_minmax(9rem,0.5fr)_minmax(16rem,1fr)_minmax(18rem,1.1fr)_auto] dark:hover:bg-neutral-950/70"
                        >
                            <div class="flex min-w-0 items-center gap-4">
                                <HotelAvatar :hotel="hotel" size="md" />
                                <div class="min-w-0">
                                    <Link
                                        :href="
                                            route(
                                                'hotels.management.index',
                                                hotel.id,
                                            )
                                        "
                                        prefetch
                                        class="hover:text-primary-700 focus-visible:ring-primary-500 dark:hover:text-primary-300 block truncate text-base font-semibold text-neutral-950 transition-colors focus-visible:rounded focus-visible:ring-2 focus-visible:outline-hidden dark:text-white"
                                    >
                                        {{ hotel.business_name }}
                                    </Link>
                                    <p
                                        class="mt-1 text-xs font-medium text-neutral-600 tabular-nums dark:text-neutral-400"
                                    >
                                        {{
                                            t(
                                                'hotels.pages.index.hotel_identifier',
                                                { id: hotel.id },
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="min-w-0">
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('hotels.fields.tin.label') }}
                                </p>
                                <p
                                    class="truncate text-sm font-medium text-neutral-800 tabular-nums dark:text-neutral-200"
                                >
                                    {{ hotel.tin }}
                                </p>
                            </div>

                            <div class="min-w-0">
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('hotels.fields.address.label') }}
                                </p>
                                <p
                                    class="flex items-start gap-2 text-sm leading-5 text-neutral-700 dark:text-neutral-300"
                                >
                                    <MapPinIcon
                                        class="mt-0.5 h-4 w-4 shrink-0 text-neutral-500"
                                    />
                                    <span class="line-clamp-2">{{
                                        hotel.address ?? t('app.not_provided')
                                    }}</span>
                                </p>
                            </div>

                            <div class="grid min-w-0 gap-1.5">
                                <p
                                    class="mb-1 text-xs font-semibold text-neutral-600 lg:hidden dark:text-neutral-400"
                                >
                                    {{ t('hotels.pages.index.contact') }}
                                </p>
                                <p
                                    class="flex min-w-0 items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300"
                                >
                                    <EnvelopeIcon
                                        class="h-4 w-4 shrink-0 text-neutral-500"
                                    />
                                    <span class="truncate">{{
                                        hotel.email ?? t('app.not_provided')
                                    }}</span>
                                </p>
                                <p
                                    class="flex min-w-0 items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    <PhoneIcon
                                        class="h-4 w-4 shrink-0 text-neutral-500"
                                    />
                                    <span class="truncate tabular-nums">{{
                                        hotel.phone ??
                                        hotel.mobile ??
                                        t('app.not_provided')
                                    }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2 lg:justify-end">
                                <ActionLink
                                    :href="
                                        route(
                                            'hotels.management.index',
                                            hotel.id,
                                        )
                                    "
                                    size="sm"
                                    prefetch
                                >
                                    <ArrowTopRightOnSquareIcon
                                        class="h-4 w-4"
                                    />
                                    {{ t('hotels.actions.open') }}
                                </ActionLink>

                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="focus-visible:ring-primary-500 inline-flex h-11 w-11 items-center justify-center rounded-lg border border-neutral-300 bg-white text-neutral-600 shadow-sm transition-colors hover:border-neutral-400 hover:bg-neutral-50 hover:text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none sm:h-9 sm:w-9 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:border-neutral-600 dark:hover:bg-neutral-800 dark:hover:text-white"
                                            :aria-label="
                                                t(
                                                    'hotels.pages.index.open_actions',
                                                    {
                                                        name: hotel.business_name,
                                                    },
                                                )
                                            "
                                        >
                                            <EllipsisVerticalIcon
                                                class="h-5 w-5"
                                            />
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="
                                                route('hotels.show', hotel.id)
                                            "
                                        >
                                            <span
                                                class="flex items-center gap-2"
                                            >
                                                <EyeIcon class="h-4 w-4" />
                                                {{ t('hotels.actions.view') }}
                                            </span>
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="
                                                route('hotels.edit', hotel.id)
                                            "
                                        >
                                            <span
                                                class="flex items-center gap-2"
                                            >
                                                <PencilSquareIcon
                                                    class="h-4 w-4"
                                                />
                                                {{ t('hotels.actions.edit') }}
                                            </span>
                                        </DropdownLink>
                                        <button
                                            type="button"
                                            class="text-danger-700 hover:bg-danger-50 focus:bg-danger-50 dark:text-danger-300 dark:hover:bg-danger-900/30 dark:focus:bg-danger-900/30 flex min-h-11 w-full items-center gap-2 px-4 py-2 text-start text-sm font-medium transition-colors focus:outline-hidden motion-reduce:transition-none"
                                            @click="requestDeletion(hotel)"
                                        >
                                            <TrashIcon class="h-4 w-4" />
                                            {{ t('hotels.actions.delete') }}
                                        </button>
                                    </template>
                                </Dropdown>
                            </div>
                        </li>
                    </ul>

                    <div
                        v-else
                        class="flex min-h-96 flex-col items-center justify-center px-6 py-16 text-center"
                    >
                        <span
                            class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                        >
                            <BuildingOffice2Icon class="h-7 w-7" />
                        </span>
                        <h2
                            class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('hotels.pages.index.no_hotels') }}
                        </h2>
                        <p
                            class="mt-2 max-w-md text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('hotels.pages.index.no_hotels_hint') }}
                        </p>
                        <ActionLink :href="route('hotels.create')" class="mt-6">
                            <PlusIcon class="h-4 w-4" />
                            {{ t('hotels.actions.create') }}
                        </ActionLink>
                    </div>

                    <Pagination
                        v-if="hotels.data.length > 0"
                        :pagination="hotels"
                    />
                </section>
            </div>
        </div>

        <Modal
            :show="selectedHotel !== null"
            max-width="md"
            aria-labelledby="delete-hotel-title"
            @close="closeDeletion"
        >
            <div class="p-6 sm:p-7">
                <span
                    class="bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300 flex h-11 w-11 items-center justify-center rounded-xl"
                >
                    <TrashIcon class="h-5 w-5" />
                </span>
                <h2
                    id="delete-hotel-title"
                    class="mt-5 text-xl font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white"
                >
                    {{ t('hotels.pages.index.delete_title') }}
                </h2>
                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{
                        selectedHotel
                            ? t('hotels.pages.index.delete_confirm', {
                                  name: selectedHotel.business_name,
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
                    >
                        {{ t('app.cancel') }}
                    </SecondaryButton>
                    <DangerButton :disabled="deleting" @click="destroy">
                        <TrashIcon class="h-4 w-4" />
                        {{
                            deleting
                                ? t('app.deleting')
                                : t('hotels.actions.delete')
                        }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </HotelLayout>
</template>
