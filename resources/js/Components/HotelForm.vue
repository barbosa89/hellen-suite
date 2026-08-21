<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import HotelAvatar from '@/Components/HotelAvatar.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import UploadInput from '@/Components/UploadInput.vue';
import { CheckIcon, ChevronLeftIcon } from '@heroicons/vue/24/outline';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    mode: {
        type: String,
        required: true,
        validator: (value) => ['create', 'edit'].includes(value),
    },
    hotel: {
        type: Object,
        default: null,
    },
});

const { t } = useI18n();
const isEditing = computed(() => props.mode === 'edit');

const form = useForm({
    business_name: props.hotel?.business_name ?? '',
    tin: props.hotel?.tin ?? '',
    address: props.hotel?.address ?? '',
    phone: props.hotel?.phone ?? '',
    mobile: props.hotel?.mobile ?? '',
    email: props.hotel?.email ?? '',
    image: null,
});

const cancelHref = computed(() =>
    isEditing.value
        ? route('hotels.show', props.hotel.id)
        : route('hotels.index'),
);

function submit() {
    if (isEditing.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            route('hotels.update', props.hotel.id),
            {
                forceFormData: true,
                onSuccess: () => form.reset('image'),
            },
        );

        return;
    }

    form.post(route('hotels.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <form
        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
        @submit.prevent="submit"
    >
        <div class="grid xl:grid-cols-[minmax(0,1.55fr)_minmax(20rem,0.65fr)]">
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                <section class="p-5 sm:p-7 lg:p-8">
                    <div class="mb-6">
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('hotels.form.identity.title') }}
                        </h2>
                        <p
                            class="mt-1 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('hotels.form.identity.description') }}
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid content-start gap-2">
                            <InputLabel
                                for="business_name"
                                :value="t('hotels.fields.business_name.label')"
                                required
                            />
                            <TextInput
                                id="business_name"
                                v-model="form.business_name"
                                name="business_name"
                                type="text"
                                class="w-full"
                                required
                                autofocus
                                autocomplete="organization"
                                :invalid="Boolean(form.errors.business_name)"
                                :aria-invalid="
                                    Boolean(form.errors.business_name)
                                "
                                aria-describedby="business-name-error"
                            />
                            <InputError
                                id="business-name-error"
                                :message="form.errors.business_name"
                            />
                        </div>

                        <div class="grid content-start gap-2">
                            <InputLabel
                                for="tin"
                                :value="t('hotels.fields.tin.label')"
                                required
                            />
                            <TextInput
                                id="tin"
                                v-model="form.tin"
                                name="tin"
                                type="text"
                                class="w-full tabular-nums"
                                required
                                autocomplete="off"
                                :invalid="Boolean(form.errors.tin)"
                                :aria-invalid="Boolean(form.errors.tin)"
                                aria-describedby="tin-error"
                            />
                            <InputError
                                id="tin-error"
                                :message="form.errors.tin"
                            />
                        </div>
                    </div>
                </section>

                <section class="p-5 sm:p-7 lg:p-8">
                    <div class="mb-6">
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('hotels.form.contact.title') }}
                        </h2>
                        <p
                            class="mt-1 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('hotels.form.contact.description') }}
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid content-start gap-2 md:col-span-2">
                            <InputLabel
                                for="address"
                                :value="t('hotels.fields.address.label')"
                            />
                            <TextInput
                                id="address"
                                v-model="form.address"
                                name="address"
                                type="text"
                                class="w-full"
                                autocomplete="street-address"
                                :invalid="Boolean(form.errors.address)"
                                :aria-invalid="Boolean(form.errors.address)"
                                aria-describedby="address-error"
                            />
                            <InputError
                                id="address-error"
                                :message="form.errors.address"
                            />
                        </div>

                        <div class="grid content-start gap-2">
                            <InputLabel
                                for="phone"
                                :value="t('hotels.fields.phone.label')"
                            />
                            <TextInput
                                id="phone"
                                v-model="form.phone"
                                name="phone"
                                type="tel"
                                class="w-full tabular-nums"
                                autocomplete="tel"
                                :invalid="Boolean(form.errors.phone)"
                                :aria-invalid="Boolean(form.errors.phone)"
                                aria-describedby="phone-error"
                            />
                            <InputError
                                id="phone-error"
                                :message="form.errors.phone"
                            />
                        </div>

                        <div class="grid content-start gap-2">
                            <InputLabel
                                for="mobile"
                                :value="t('hotels.fields.mobile.label')"
                            />
                            <TextInput
                                id="mobile"
                                v-model="form.mobile"
                                name="mobile"
                                type="tel"
                                class="w-full tabular-nums"
                                autocomplete="tel"
                                :invalid="Boolean(form.errors.mobile)"
                                :aria-invalid="Boolean(form.errors.mobile)"
                                aria-describedby="mobile-error"
                            />
                            <InputError
                                id="mobile-error"
                                :message="form.errors.mobile"
                            />
                        </div>

                        <div class="grid content-start gap-2 md:col-span-2">
                            <InputLabel
                                for="email"
                                :value="t('hotels.fields.email.label')"
                            />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                name="email"
                                type="email"
                                class="w-full"
                                autocomplete="email"
                                :invalid="Boolean(form.errors.email)"
                                :aria-invalid="Boolean(form.errors.email)"
                                aria-describedby="email-error"
                            />
                            <InputError
                                id="email-error"
                                :message="form.errors.email"
                            />
                        </div>
                    </div>
                </section>
            </div>

            <aside
                class="border-t border-neutral-200 bg-neutral-50 p-5 sm:p-7 xl:border-s xl:border-t-0 dark:border-neutral-800 dark:bg-neutral-950/60"
            >
                <div class="mb-6 flex items-start gap-4">
                    <HotelAvatar v-if="hotel" :hotel="hotel" size="lg" />
                    <div>
                        <h2
                            class="text-lg font-semibold text-neutral-950 dark:text-white"
                        >
                            {{ t('hotels.form.image.title') }}
                        </h2>
                        <p
                            class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('hotels.form.image.description') }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-2">
                    <InputLabel
                        for="image"
                        :value="t('hotels.fields.image.label')"
                    />
                    <UploadInput
                        id="image"
                        v-model="form.image"
                        name="image"
                        accept="image/png,image/jpeg,image/webp"
                        :invalid="Boolean(form.errors.image)"
                        :aria-invalid="Boolean(form.errors.image)"
                        aria-describedby="image-error"
                    />
                    <InputError id="image-error" :message="form.errors.image" />
                </div>

                <div v-if="form.progress" class="mt-5" aria-live="polite">
                    <div
                        class="mb-2 flex items-center justify-between gap-3 text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                    >
                        <span>{{ t('app.upload.uploading') }}</span>
                        <span class="tabular-nums"
                            >{{ form.progress.percentage }}%</span
                        >
                    </div>
                    <progress
                        :value="form.progress.percentage"
                        max="100"
                        class="accent-primary-600 h-1.5 w-full overflow-hidden rounded-full"
                    >
                        {{ form.progress.percentage }}%
                    </progress>
                </div>
            </aside>
        </div>

        <footer
            class="flex flex-col-reverse gap-3 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
        >
            <ActionLink :href="cancelHref" variant="ghost">
                <ChevronLeftIcon class="h-4 w-4" />
                {{ t('app.cancel') }}
            </ActionLink>

            <PrimaryButton type="submit" :disabled="form.processing">
                <CheckIcon class="h-4 w-4" />
                {{
                    form.processing
                        ? t('app.saving')
                        : isEditing
                          ? t('app.save')
                          : t('hotels.actions.create')
                }}
            </PrimaryButton>
        </footer>
    </form>
</template>
