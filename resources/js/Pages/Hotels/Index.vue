<script setup>
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    BuildingOffice2Icon,
    CheckCircleIcon,
    EyeIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    hotels: {
        type: Object,
        required: true,
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

const primaryLinkClasses =
    'inline-flex items-center gap-2 rounded-md border border-transparent bg-primary-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-primary-500 focus:bg-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:outline-hidden active:bg-primary-900 dark:bg-primary-500 dark:text-neutral-950 dark:hover:bg-primary-400 dark:focus:bg-primary-400 dark:active:bg-primary-600 dark:focus:ring-offset-neutral-950';

const iconLinkClasses =
    'inline-flex h-8 w-8 items-center justify-center rounded-md border border-neutral-200 bg-white text-neutral-500 shadow-xs transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-700 focus:ring-2 focus:ring-primary-500 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-100';

const iconDangerClasses =
    'inline-flex h-8 w-8 items-center justify-center rounded-md border border-danger-200 bg-white text-danger-600 shadow-xs transition duration-150 ease-in-out hover:bg-danger-50 hover:text-danger-700 focus:ring-2 focus:ring-danger-500 focus:outline-hidden dark:border-danger-800 dark:bg-neutral-800 dark:text-danger-400 dark:hover:bg-danger-900/20 dark:hover:text-danger-300';

function destroy(hotel) {
    if (
        confirm(
            t('hotels.pages.index.delete_confirm', {
                name: hotel.business_name,
            }),
        )
    ) {
        router.delete(route('hotels.destroy', hotel.id));
    }
}
</script>

<template>
    <Head :title="t('hotels.title')" />

    <DefaultLayout>
        <template #header>
            <h2
                class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
            >
                {{ t('hotels.title') }}
            </h2>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div
                    v-if="flash.success"
                    class="border-success-200 bg-success-50 text-success-700 dark:border-success-800 dark:bg-success-900/30 dark:text-success-300 flex items-center gap-3 rounded-md border px-4 py-3 text-sm"
                    role="status"
                >
                    <CheckCircleIcon class="h-5 w-5 shrink-0" />
                    {{ flash.success }}
                </div>

                <div
                    class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
                >
                    <div
                        class="flex items-center justify-between gap-3 border-b border-neutral-200 px-6 py-4 dark:border-neutral-700"
                    >
                        <p
                            class="text-sm text-neutral-600 dark:text-neutral-300"
                        >
                            <span
                                class="font-semibold text-neutral-900 dark:text-neutral-100"
                            >
                                {{ hotels.total }}
                            </span>
                            {{
                                t('hotels.pages.index.registered', hotels.total)
                            }}
                        </p>

                        <Link
                            :href="route('hotels.create')"
                            :class="primaryLinkClasses"
                        >
                            <PlusIcon class="h-4 w-4" />
                            {{ t('hotels.actions.create') }}
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700"
                        >
                            <thead class="bg-neutral-50 dark:bg-neutral-900">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ t('hotels.fields.id.label') }}
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{
                                            t(
                                                'hotels.fields.business_name.label',
                                            )
                                        }}
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ t('hotels.fields.tin.label') }}
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ t('hotels.fields.email.label') }}
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ t('hotels.fields.phone.label') }}
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ t('hotels.fields.mobile.label') }}
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ t('app.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800"
                            >
                                <tr
                                    v-for="hotel in hotels.data"
                                    :key="hotel.id"
                                    class="transition-colors duration-150 hover:bg-neutral-50 dark:hover:bg-neutral-700/40"
                                >
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-neutral-500 tabular-nums dark:text-neutral-400"
                                    >
                                        {{ hotel.id }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm font-medium whitespace-nowrap text-neutral-900 dark:text-neutral-100"
                                    >
                                        {{ hotel.business_name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-neutral-500 tabular-nums dark:text-neutral-400"
                                    >
                                        {{ hotel.tin }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-neutral-500 dark:text-neutral-400"
                                    >
                                        {{ hotel.email ?? '—' }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-neutral-500 dark:text-neutral-400"
                                    >
                                        {{ hotel.phone ?? '—' }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-neutral-500 dark:text-neutral-400"
                                    >
                                        {{ hotel.mobile ?? '—' }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right text-sm whitespace-nowrap"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'hotels.show',
                                                        hotel.id,
                                                    )
                                                "
                                                :class="iconLinkClasses"
                                                :aria-label="
                                                    t('hotels.actions.view')
                                                "
                                                :title="
                                                    t('hotels.actions.view')
                                                "
                                            >
                                                <EyeIcon class="h-4 w-4" />
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'hotels.edit',
                                                        hotel.id,
                                                    )
                                                "
                                                :class="iconLinkClasses"
                                                :aria-label="
                                                    t('hotels.actions.edit')
                                                "
                                                :title="
                                                    t('hotels.actions.edit')
                                                "
                                            >
                                                <PencilSquareIcon
                                                    class="h-4 w-4"
                                                />
                                            </Link>
                                            <button
                                                type="button"
                                                :class="iconDangerClasses"
                                                :aria-label="
                                                    t('hotels.actions.delete')
                                                "
                                                :title="
                                                    t('hotels.actions.delete')
                                                "
                                                @click="destroy(hotel)"
                                            >
                                                <TrashIcon class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="hotels.data.length === 0">
                                    <td colspan="7">
                                        <div
                                            class="flex flex-col items-center gap-3 px-6 py-16 text-center"
                                        >
                                            <BuildingOffice2Icon
                                                class="h-12 w-12 text-neutral-300 dark:text-neutral-600"
                                            />
                                            <div>
                                                <p
                                                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100"
                                                >
                                                    {{
                                                        t(
                                                            'hotels.pages.index.no_hotels',
                                                        )
                                                    }}
                                                </p>
                                                <p
                                                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                                >
                                                    {{
                                                        t(
                                                            'hotels.pages.index.no_hotels_hint',
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <Link
                                                :href="route('hotels.create')"
                                                :class="primaryLinkClasses"
                                            >
                                                <PlusIcon class="h-4 w-4" />
                                                {{ t('hotels.actions.create') }}
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="flex flex-col items-center justify-between gap-4 border-t border-neutral-200 px-6 py-4 sm:flex-row dark:border-neutral-700"
                    >
                        <p
                            class="text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('app.pagination.showing') }}
                            <span
                                class="font-semibold text-neutral-900 dark:text-neutral-100"
                            >
                                {{ hotels.from }}
                            </span>
                            {{ t('app.pagination.to') }}
                            <span
                                class="font-semibold text-neutral-900 dark:text-neutral-100"
                            >
                                {{ hotels.to }}
                            </span>
                            {{ t('app.pagination.of') }}
                            <span
                                class="font-semibold text-neutral-900 dark:text-neutral-100"
                            >
                                {{ hotels.total }}
                            </span>
                        </p>

                        <nav
                            class="flex flex-wrap items-center justify-center gap-1"
                            :aria-label="t('app.pagination.pagination')"
                        >
                            <template
                                v-for="link in hotels.links"
                                :key="link.label"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="focus:ring-primary-500 min-w-8 rounded-md px-3 py-2 text-sm font-medium transition duration-150 ease-in-out focus:ring-2 focus:outline-hidden"
                                    :class="
                                        link.active
                                            ? 'bg-primary-800 dark:bg-primary-500 text-white dark:text-neutral-950'
                                            : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-neutral-100'
                                    "
                                >
                                    <span v-html="link.label" />
                                </Link>
                                <span
                                    v-else
                                    class="min-w-8 cursor-default rounded-md px-3 py-2 text-sm font-medium text-neutral-400 dark:text-neutral-600"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
