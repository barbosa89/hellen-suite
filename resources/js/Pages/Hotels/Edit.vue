<script setup>
import HotelLayout from '@/Layouts/HotelLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import UploadInput from '@/Components/UploadInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    business_name: props.hotel.business_name,
    tin: props.hotel.tin,
    address: props.hotel.address,
    phone: props.hotel.phone,
    mobile: props.hotel.mobile,
    email: props.hotel.email,
    image: null,
});

function submit() {
    form
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(route('hotels.update', props.hotel.id), {
            forceFormData: true,
            onSuccess: () => form.reset('image'),
        });
}
</script>

<template>
    <Head :title="t('hotels.pages.edit.heading')" />

    <HotelLayout>
        <template #header>
            <h2
                class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
            >
                {{ t('hotels.pages.edit.heading') }}
            </h2>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <div
                    class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
                >
                    <form @submit.prevent="submit">
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
                                        v-model="form.business_name"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.business_name"
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
                                        v-model="form.tin"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.tin"
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
                                    v-model="form.address"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.address"
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
                                        v-model="form.phone"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.phone"
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
                                        v-model="form.mobile"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.mobile"
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
                                        v-model="form.email"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.email"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="image"
                                        :value="t('hotels.fields.image.label')"
                                    />
                                    <img
                                        v-if="props.hotel.image"
                                        :src="props.hotel.image"
                                        :alt="props.hotel.business_name"
                                        class="mt-2 h-24 w-24 rounded-md border border-neutral-200 object-cover dark:border-neutral-700"
                                    />
                                    <UploadInput
                                        id="image"
                                        name="image"
                                        class="mt-1 block w-full"
                                        accept="image/png,image/jpeg,image/webp"
                                        v-model="form.image"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.image"
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
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                {{ t('app.save') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </HotelLayout>
</template>
