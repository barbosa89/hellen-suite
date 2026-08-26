<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowRightIcon,
    CalendarDaysIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({ hotel: Object, stays: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('stays.title')" />

    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('stays.title')"
                    :description="
                        t('stays.pages.index.description', {
                            hotel: hotel.business_name,
                        })
                    "
                >
                    <template #actions>
                        <ActionLink
                            :href="route('hotels.stays.create', hotel.id)"
                        >
                            <PlusIcon class="h-4 w-4" />
                            {{ t('stays.actions.create') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <section
                    v-if="stays.data.length"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="stay in stays.data"
                            :key="stay.id"
                            class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                        >
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <Link
                                        :href="
                                            route('hotels.stays.show', [
                                                hotel.id,
                                                stay.id,
                                            ])
                                        "
                                        class="focus-visible:ring-primary-500 hover:text-primary-700 dark:hover:text-primary-300 rounded text-base font-semibold text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden dark:text-white"
                                    >
                                        {{ stay.responsible_guest.first_name }}
                                        {{ stay.responsible_guest.last_name }}
                                    </Link>
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            stay.status === 'active'
                                                ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                                                : 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300'
                                        "
                                    >
                                        {{
                                            stay.status === 'active'
                                                ? t('stays.pages.show.active')
                                                : t(
                                                      'stays.pages.show.checked_out',
                                                  )
                                        }}
                                    </span>
                                </div>
                                <p
                                    class="mt-2 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        t(
                                            'stays.fields.expected_check_out_on.label',
                                        )
                                    }}:
                                    <span class="tabular-nums">{{
                                        stay.expected_check_out_on
                                    }}</span>
                                    · {{ stay.active_room_occupancies_count }}
                                    {{
                                        t(
                                            'stays.fields.room.label',
                                        ).toLowerCase()
                                    }}
                                </p>
                            </div>
                            <ActionLink
                                :href="
                                    route('hotels.stays.show', [
                                        hotel.id,
                                        stay.id,
                                    ])
                                "
                                variant="secondary"
                                size="sm"
                            >
                                {{ t('guests.actions.view') }}
                                <ArrowRightIcon class="h-4 w-4" />
                            </ActionLink>
                        </li>
                    </ul>
                    <Pagination :pagination="stays" />
                </section>

                <section
                    v-else
                    class="flex min-h-80 flex-col items-center justify-center rounded-2xl bg-white px-6 py-12 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                        ><CalendarDaysIcon class="h-7 w-7"
                    /></span>
                    <h2
                        class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('stays.pages.index.empty_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('stays.pages.index.empty') }}
                    </p>
                    <ActionLink
                        :href="route('hotels.stays.create', hotel.id)"
                        class="mt-6"
                        ><PlusIcon class="h-4 w-4" />{{
                            t('stays.actions.create')
                        }}</ActionLink
                    >
                </section>
            </div>
        </div>
    </DefaultLayout>
</template>
