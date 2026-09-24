<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import HotelLayout from '@/Layouts/HotelLayout.vue';
import {
    ArrowLeftIcon,
    CheckIcon,
    CurrencyDollarIcon,
} from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    currency: { type: String, default: null },
    currencies: { type: Array, required: true },
    flash: { type: Object, default: () => ({}) },
    missingSettings: { type: Array, required: true },
});

const { t } = useI18n();
const form = useForm({ currency: props.currency ?? '' });

function submit() {
    form.put(route('settings.update'));
}

function currencyCode(currency) {
    return currency.code;
}

function currencyLabel(currency) {
    return `${currency.code} — ${currency.name}`;
}
</script>

<template>
    <Head :title="t('settings.pages.edit.heading')" />
    <HotelLayout>
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('settings.pages.edit.heading')"
                    :description="t('settings.pages.edit.description')"
                >
                    <template #actions>
                        <ActionLink
                            :href="route('hotels.index')"
                            variant="ghost"
                        >
                            <ArrowLeftIcon class="h-4 w-4" />
                            {{ t('app.navigation.all_hotels') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <FlashMessage v-if="flash.success" :message="flash.success" />
                <FlashMessage
                    v-if="flash.error"
                    :message="flash.error"
                    variant="error"
                />
                <FlashMessage
                    v-if="missingSettings.length > 0 && !flash.error"
                    :message="t('settings.messages.configuration_required')"
                    variant="error"
                />

                <form
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    @submit.prevent="submit"
                >
                    <div
                        class="grid xl:grid-cols-[minmax(0,1fr)_minmax(20rem,0.55fr)]"
                    >
                        <section class="p-5 sm:p-7 lg:p-8">
                            <div class="mb-6 flex items-start gap-4">
                                <span
                                    class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                    ><CurrencyDollarIcon class="h-5 w-5"
                                /></span>
                                <div>
                                    <h2
                                        class="text-lg font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{ t('settings.currency.title') }}
                                    </h2>
                                    <p
                                        class="mt-1 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{ t('settings.currency.description') }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid max-w-2xl gap-2">
                                <InputLabel
                                    for="currency"
                                    :value="t('settings.currency.label')"
                                    required
                                />
                                <VueSelect
                                    v-model="form.currency"
                                    input-id="currency"
                                    class="currency-select"
                                    :class="
                                        form.errors.currency
                                            ? 'currency-select--error'
                                            : null
                                    "
                                    :options="currencies"
                                    :reduce="currencyCode"
                                    :get-option-label="currencyLabel"
                                    :placeholder="
                                        t('settings.currency.placeholder')
                                    "
                                    :clearable="false"
                                >
                                    <template #search="{ attributes, events }">
                                        <input
                                            class="vs__search"
                                            v-bind="attributes"
                                            :required="!form.currency"
                                            :aria-invalid="
                                                Boolean(form.errors.currency)
                                            "
                                            aria-describedby="currency-help currency-error"
                                            v-on="events"
                                        />
                                    </template>
                                    <template #no-options>
                                        {{ t('settings.currency.no_results') }}
                                    </template>
                                </VueSelect>
                                <p
                                    id="currency-help"
                                    class="text-xs leading-5 text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ t('settings.currency.hint') }}
                                </p>
                                <InputError
                                    id="currency-error"
                                    :message="form.errors.currency"
                                />
                            </div>
                        </section>

                        <aside
                            class="border-t border-neutral-200 bg-neutral-50 p-5 sm:p-7 xl:border-s xl:border-t-0 dark:border-neutral-800 dark:bg-neutral-950/60"
                        >
                            <h2
                                class="text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('settings.currency.reference_title') }}
                            </h2>
                            <p
                                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{
                                    t('settings.currency.reference_description')
                                }}
                            </p>
                        </aside>
                    </div>
                    <footer
                        class="flex justify-end border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
                    >
                        <PrimaryButton type="submit" :disabled="form.processing"
                            ><CheckIcon class="h-4 w-4" />{{
                                form.processing
                                    ? t('app.saving')
                                    : t('app.save')
                            }}</PrimaryButton
                        >
                    </footer>
                </form>
            </div>
        </div>
    </HotelLayout>
</template>

<style>
.currency-select {
    --vs-border-color: var(--color-neutral-300);
    --vs-border-radius: 0.5rem;
    --vs-controls-color: var(--color-neutral-500);
    --vs-dropdown-bg: white;
    --vs-dropdown-color: var(--color-neutral-900);
    --vs-dropdown-option--active-bg: var(--color-primary-600);
    --vs-dropdown-option--active-color: white;
    --vs-search-input-color: var(--color-neutral-900);
    --vs-search-input-placeholder-color: var(--color-neutral-500);
    --vs-selected-color: var(--color-neutral-900);
}

.currency-select .vs__dropdown-toggle {
    min-height: 2.75rem;
    padding: 0.25rem 0.5rem;
    background: white;
    box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
    transition:
        border-color 150ms,
        box-shadow 150ms;
}

.currency-select .vs__dropdown-toggle:focus-within {
    border-color: var(--color-primary-500);
    box-shadow: 0 0 0 3px rgb(0 188 212 / 0.2);
}

.currency-select .vs__search,
.currency-select .vs__selected {
    margin: 0;
    padding: 0.375rem 0.25rem;
    font-size: 1rem;
    line-height: 1.25rem;
}

.currency-select .vs__search {
    border: 0;
    box-shadow: none;
}

.currency-select .vs__dropdown-menu {
    margin-top: 0.25rem;
    border-color: var(--color-neutral-200);
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.16);
}

.currency-select .vs__dropdown-option {
    padding: 0.625rem 0.875rem;
    white-space: normal;
}

.currency-select--error {
    --vs-border-color: var(--color-danger-500);
}

.dark .currency-select {
    --vs-border-color: var(--color-neutral-700);
    --vs-controls-color: var(--color-neutral-400);
    --vs-dropdown-bg: var(--color-neutral-950);
    --vs-dropdown-color: var(--color-neutral-100);
    --vs-search-input-color: var(--color-neutral-100);
    --vs-search-input-placeholder-color: var(--color-neutral-500);
    --vs-selected-color: var(--color-neutral-100);
}

.dark .currency-select .vs__dropdown-toggle {
    background: var(--color-neutral-950);
}

.dark .currency-select .vs__dropdown-menu {
    border-color: var(--color-neutral-700);
}

.dark .currency-select--error {
    --vs-border-color: var(--color-danger-500);
}
</style>
