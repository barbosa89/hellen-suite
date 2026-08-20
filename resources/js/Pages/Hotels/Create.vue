<script setup>
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('hotels.pages.create.heading')" />

    <DefaultLayout>
        <template #header>
            <h2
                class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
            >
                {{ t('hotels.pages.create.heading') }}
            </h2>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <div
                    class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
                >
                    <Form
                        :action="route('hotels.store')"
                        method="post"
                        class="contents"
                        #default="{ errors, processing }"
                    >
                        <div class="space-y-6 p-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <InputLabel
                                        for="business_name"
                                        :value="
                                            t(
                                                'hotels.fields.business_name.label',
                                            )
                                        "
                                    />
                                    <TextInput
                                        id="business_name"
                                        name="business_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        autofocus
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.business_name"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tin"
                                        :value="t('hotels.fields.tin.label')"
                                    />
                                    <TextInput
                                        id="tin"
                                        name="tin"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.tin"
                                    />
                                </div>
                            </div>

                            <div>
                                <InputLabel
                                    for="address"
                                    :value="t('hotels.fields.address.label')"
                                />
                                <TextInput
                                    id="address"
                                    name="address"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="errors.address"
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <InputLabel
                                        for="phone"
                                        :value="t('hotels.fields.phone.label')"
                                    />
                                    <TextInput
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        class="mt-1 block w-full"
                                        autocomplete="tel"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.phone"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="mobile"
                                        :value="t('hotels.fields.mobile.label')"
                                    />
                                    <TextInput
                                        id="mobile"
                                        name="mobile"
                                        type="tel"
                                        class="mt-1 block w-full"
                                        autocomplete="tel"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.mobile"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <InputLabel
                                        for="email"
                                        :value="t('hotels.fields.email.label')"
                                    />
                                    <TextInput
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        autocomplete="email"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.email"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="image"
                                        :value="t('hotels.fields.image.label')"
                                    />
                                    <TextInput
                                        id="image"
                                        name="image"
                                        type="url"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.image"
                                    />
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between gap-3 border-t border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900"
                        >
                            <Link
                                :href="route('hotels.index')"
                                class="focus:ring-primary-500 inline-flex items-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold tracking-widest text-neutral-700 uppercase shadow-xs transition duration-150 ease-in-out hover:bg-neutral-50 focus:ring-2 focus:ring-offset-2 focus:outline-hidden dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-950"
                            >
                                {{ t('app.back') }}
                            </Link>

                            <PrimaryButton
                                :class="{ 'opacity-25': processing }"
                                :disabled="processing"
                            >
                                {{ t('hotels.actions.create') }}
                            </PrimaryButton>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
