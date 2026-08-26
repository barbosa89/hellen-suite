<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    MagnifyingGlassIcon,
    PlusIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({ hotel: Object, guests: Object, filters: Object });
const { t } = useI18n();
const search = ref(props.filters.search ?? '');
let searchTimeout;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(
        () =>
            router.get(
                route('hotels.guests.index', props.hotel.id),
                { search: value },
                { preserveState: true, replace: true },
            ),
        250,
    );
});
</script>

<template>
    <Head :title="t('guests.title')" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('guests.title')"
                    :description="
                        t('guests.pages.index.description', {
                            hotel: hotel.business_name,
                        })
                    "
                    ><template #actions
                        ><ActionLink
                            :href="route('hotels.guests.create', hotel.id)"
                            ><PlusIcon class="h-4 w-4" />{{
                                t('guests.actions.create')
                            }}</ActionLink
                        ></template
                    ></PageHeader
                >
                <section
                    class="rounded-2xl bg-white p-4 shadow-sm dark:bg-neutral-900"
                >
                    <label class="relative block"
                        ><MagnifyingGlassIcon
                            class="absolute start-3 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-500" /><TextInput
                            v-model="search"
                            class="w-full ps-10"
                            :placeholder="t('guests.pages.index.search')"
                    /></label>
                </section>
                <section
                    v-if="guests.data.length"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="guest in guests.data"
                            :key="guest.id"
                            class="grid gap-4 px-5 py-5 sm:grid-cols-[minmax(0,1fr)_minmax(12rem,.7fr)_auto] sm:items-center xl:px-6"
                        >
                            <div>
                                <Link
                                    :href="
                                        route('hotels.guests.show', [
                                            hotel.id,
                                            guest.id,
                                        ])
                                    "
                                    class="focus-visible:ring-primary-500 hover:text-primary-700 dark:hover:text-primary-300 rounded text-base font-semibold text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden dark:text-white"
                                    >{{ guest.first_name }}
                                    {{ guest.last_name }}</Link
                                >
                                <p
                                    class="mt-1 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                >
                                    {{ guest.identification_type.code }} ·
                                    {{ guest.identification_number }}
                                </p>
                            </div>
                            <div
                                class="text-sm text-neutral-600 dark:text-neutral-400"
                            >
                                {{
                                    guest.mobile ||
                                    guest.email ||
                                    t('app.not_provided')
                                }}
                            </div>
                            <ActionLink
                                :href="
                                    route('hotels.guests.show', [
                                        hotel.id,
                                        guest.id,
                                    ])
                                "
                                variant="secondary"
                                size="sm"
                                >{{ t('guests.actions.view') }}</ActionLink
                            >
                        </li>
                    </ul>
                    <Pagination :pagination="guests" />
                </section>
                <section
                    v-else
                    class="flex min-h-80 flex-col items-center justify-center rounded-2xl bg-white px-6 py-12 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                        ><UserGroupIcon class="h-7 w-7"
                    /></span>
                    <h2
                        class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('guests.pages.index.empty_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('guests.pages.index.empty') }}
                    </p>
                    <ActionLink
                        :href="route('hotels.guests.create', hotel.id)"
                        class="mt-6"
                        ><PlusIcon class="h-4 w-4" />{{
                            t('guests.actions.create')
                        }}</ActionLink
                    >
                </section>
            </div>
        </div>
    </DefaultLayout>
</template>
