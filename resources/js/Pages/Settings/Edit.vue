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

const props = defineProps({
    currency: { type: String, default: null },
    currencies: { type: Array, required: true },
    flash: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const form = useForm({ currency: props.currency ?? '' });

function submit() {
    form.put(route('settings.update'));
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
                                <select
                                    id="currency"
                                    v-model="form.currency"
                                    name="currency"
                                    required
                                    class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] duration-150 focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                                    :class="
                                        form.errors.currency
                                            ? 'border-danger-500'
                                            : 'border-neutral-300 dark:border-neutral-700'
                                    "
                                    :aria-invalid="
                                        Boolean(form.errors.currency)
                                    "
                                    aria-describedby="currency-help currency-error"
                                >
                                    <option value="" disabled>
                                        {{ t('settings.currency.placeholder') }}
                                    </option>
                                    <option
                                        v-for="item in currencies"
                                        :key="item.code"
                                        :value="item.code"
                                    >
                                        {{ item.code }} — {{ item.name }}
                                    </option>
                                </select>
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
