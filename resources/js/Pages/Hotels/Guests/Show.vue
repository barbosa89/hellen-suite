<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { PencilSquareIcon, UserIcon } from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
defineProps({ hotel: Object, guest: Object, stayGuests: Object });
const { t } = useI18n();
</script>
<template>
    <Head :title="`${guest.first_name} ${guest.last_name}`" /><DefaultLayout
        :hotel="hotel"
        ><div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('guests.pages.show.heading')"
                    :description="`${guest.first_name} ${guest.last_name}`"
                    ><template #actions
                        ><ActionLink
                            :href="
                                route('hotels.guests.edit', [
                                    hotel.id,
                                    guest.id,
                                ])
                            "
                            variant="secondary"
                            ><PencilSquareIcon class="h-4 w-4" />{{
                                t('guests.actions.edit')
                            }}</ActionLink
                        ></template
                    ></PageHeader
                >
                <section
                    class="grid gap-6 rounded-2xl bg-white p-6 shadow-sm sm:grid-cols-2 dark:bg-neutral-900"
                >
                    <div>
                        <p
                            class="text-sm font-semibold text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('guests.fields.identification_number.label') }}
                        </p>
                        <p
                            class="mt-2 text-xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                        >
                            {{ guest.identification_type.code }} ·
                            {{ guest.identification_number }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-sm font-semibold text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('guests.fields.mobile.label') }}
                        </p>
                        <p
                            class="mt-2 text-base text-neutral-950 dark:text-white"
                        >
                            {{ guest.mobile || t('app.not_provided') }}
                        </p>
                        <p
                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ guest.email || t('app.not_provided') }}
                        </p>
                    </div>
                </section>
                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <div
                        class="border-b border-neutral-200 px-6 py-5 dark:border-neutral-800"
                    >
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('guests.pages.show.stays') }}
                        </h2>
                    </div>
                    <ul
                        v-if="stayGuests.data.length"
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="stayGuest in stayGuests.data"
                            :key="stayGuest.id"
                            class="flex items-center justify-between gap-4 px-6 py-4"
                        >
                            <div>
                                <p
                                    class="font-semibold text-neutral-950 dark:text-white"
                                >
                                    {{
                                        stayGuest.stay.status === 'active'
                                            ? t('stays.pages.show.active')
                                            : t('stays.pages.show.checked_out')
                                    }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ stayGuest.stay.expected_check_out_on }}
                                </p>
                            </div>
                            <Link
                                :href="
                                    route('hotels.stays.show', [
                                        hotel.id,
                                        stayGuest.stay.id,
                                    ])
                                "
                                class="text-primary-700 hover:text-primary-800 dark:text-primary-300 text-sm font-semibold"
                                >{{ t('guests.actions.view') }}</Link
                            >
                        </li>
                    </ul>
                    <div
                        v-else
                        class="px-6 py-10 text-center text-sm text-neutral-600 dark:text-neutral-400"
                    >
                        <UserIcon class="mx-auto mb-3 h-6 w-6" />{{
                            t('guests.pages.show.empty_stays')
                        }}
                    </div>
                    <Pagination
                        v-if="stayGuests.data.length"
                        :pagination="stayGuests"
                    />
                </section>
            </div></div
    ></DefaultLayout>
</template>
