<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import HotelAvatar from '@/Components/HotelAvatar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import HotelLayout from '@/Layouts/HotelLayout.vue';
import {
    ArrowLeftIcon,
    ArrowTopRightOnSquareIcon,
    BuildingOffice2Icon,
    DevicePhoneMobileIcon,
    EnvelopeIcon,
    IdentificationIcon,
    MapPinIcon,
    PencilSquareIcon,
    PhoneIcon,
    PhotoIcon,
} from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const details = [
    { key: 'business_name', icon: BuildingOffice2Icon },
    { key: 'tin', icon: IdentificationIcon, tabular: true },
    { key: 'address', icon: MapPinIcon },
    { key: 'phone', icon: PhoneIcon, tabular: true },
    { key: 'mobile', icon: DevicePhoneMobileIcon, tabular: true },
    { key: 'email', icon: EnvelopeIcon },
];
</script>

<template>
    <Head :title="props.hotel.business_name" />

    <HotelLayout>
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="mx-auto grid w-full max-w-[100rem] gap-7">
                <PageHeader
                    :title="props.hotel.business_name"
                    :description="t('hotels.pages.show.description')"
                >
                    <template #meta>
                        <span
                            class="bg-primary-50 text-primary-800 ring-primary-200 dark:bg-primary-950 dark:text-primary-300 dark:ring-primary-800 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1"
                        >
                            {{
                                t('hotels.pages.index.hotel_identifier', {
                                    id: props.hotel.id,
                                })
                            }}
                        </span>
                    </template>

                    <template #actions>
                        <ActionLink
                            :href="route('hotels.index')"
                            variant="ghost"
                        >
                            <ArrowLeftIcon class="h-4 w-4" />
                            {{ t('app.navigation.all_hotels') }}
                        </ActionLink>
                        <ActionLink
                            :href="route('hotels.edit', props.hotel.id)"
                            variant="secondary"
                        >
                            <PencilSquareIcon class="h-4 w-4" />
                            {{ t('hotels.actions.edit') }}
                        </ActionLink>
                        <ActionLink
                            :href="
                                route('hotels.management.index', props.hotel.id)
                            "
                            prefetch
                        >
                            <ArrowTopRightOnSquareIcon class="h-4 w-4" />
                            {{ t('hotels.actions.open') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="grid xl:grid-cols-[minmax(0,1.35fr)_minmax(22rem,0.65fr)]"
                    >
                        <div class="p-5 sm:p-7 lg:p-8">
                            <div class="mb-7 flex items-center gap-4">
                                <HotelAvatar :hotel="props.hotel" size="lg" />
                                <div class="min-w-0">
                                    <h2
                                        class="truncate text-xl font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{ props.hotel.business_name }}
                                    </h2>
                                    <p
                                        class="mt-1 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                    >
                                        {{ props.hotel.tin }}
                                    </p>
                                </div>
                            </div>

                            <dl
                                class="divide-y divide-neutral-200 border-y border-neutral-200 dark:divide-neutral-800 dark:border-neutral-800"
                            >
                                <div
                                    v-for="detail in details"
                                    :key="detail.key"
                                    class="grid gap-2 py-4 sm:grid-cols-[13rem_minmax(0,1fr)] sm:items-start"
                                >
                                    <dt
                                        class="flex items-center gap-2 text-sm font-semibold text-neutral-600 dark:text-neutral-400"
                                    >
                                        <component
                                            :is="detail.icon"
                                            class="h-4 w-4 shrink-0"
                                        />
                                        {{
                                            t(
                                                `hotels.fields.${detail.key}.label`,
                                            )
                                        }}
                                    </dt>
                                    <dd
                                        class="text-sm font-medium text-neutral-950 dark:text-neutral-100"
                                        :class="
                                            detail.tabular ? 'tabular-nums' : ''
                                        "
                                    >
                                        {{
                                            props.hotel[detail.key] ??
                                            t('app.not_provided')
                                        }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <figure
                            class="border-t border-neutral-200 bg-neutral-50 p-5 sm:p-7 xl:border-s xl:border-t-0 dark:border-neutral-800 dark:bg-neutral-950/60"
                        >
                            <figcaption
                                class="mb-4 flex items-center gap-2 text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                            >
                                <PhotoIcon class="h-4 w-4 text-neutral-500" />
                                {{ t('hotels.fields.image.label') }}
                            </figcaption>

                            <div
                                class="flex min-h-72 items-center justify-center overflow-hidden rounded-xl bg-white dark:bg-neutral-900"
                            >
                                <img
                                    v-if="props.hotel.image"
                                    :src="props.hotel.image"
                                    :alt="props.hotel.business_name"
                                    class="h-full max-h-[34rem] w-full object-contain"
                                    decoding="async"
                                />
                                <div
                                    v-else
                                    class="flex flex-col items-center gap-3 px-6 py-12 text-center text-neutral-600 dark:text-neutral-400"
                                >
                                    <PhotoIcon class="h-8 w-8" />
                                    <p class="text-sm">
                                        {{ t('hotels.pages.show.no_image') }}
                                    </p>
                                </div>
                            </div>
                        </figure>
                    </div>
                </section>
            </div>
        </div>
    </HotelLayout>
</template>
