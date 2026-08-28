<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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
        required: true,
    },
    roomType: {
        type: Object,
        default: null,
    },
});

const { t } = useI18n();
const isEditing = computed(() => props.mode === 'edit');
const form = useForm({
    name: props.roomType?.name ?? '',
    capacity: props.roomType?.capacity ?? '',
});

function submit() {
    if (isEditing.value) {
        form.put(
            route('hotels.room-types.update', [
                props.hotel.id,
                props.roomType.id,
            ]),
        );

        return;
    }

    form.post(route('hotels.room-types.store', props.hotel.id));
}
</script>

<template>
    <form
        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
        @submit.prevent="submit"
    >
        <section class="p-5 sm:p-7 lg:p-8">
            <div class="mb-6">
                <h2
                    class="text-lg font-semibold text-neutral-950 dark:text-white"
                >
                    {{ t('room_types.form.title') }}
                </h2>
                <p
                    class="mt-1 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{ t('room_types.form.description') }}
                </p>
            </div>

            <div class="grid max-w-3xl gap-5 md:grid-cols-2">
                <div class="grid content-start gap-2">
                    <InputLabel
                        for="name"
                        :value="t('room_types.fields.name.label')"
                        required
                    />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        name="name"
                        type="text"
                        class="w-full"
                        required
                        autofocus
                        autocomplete="off"
                        :invalid="Boolean(form.errors.name)"
                        :aria-invalid="Boolean(form.errors.name)"
                        aria-describedby="room-type-name-error"
                    />
                    <InputError
                        id="room-type-name-error"
                        :message="form.errors.name"
                    />
                </div>

                <div class="grid content-start gap-2">
                    <InputLabel
                        for="capacity"
                        :value="t('room_types.fields.capacity.label')"
                        required
                    />
                    <TextInput
                        id="capacity"
                        v-model="form.capacity"
                        name="capacity"
                        type="number"
                        min="1"
                        max="255"
                        step="1"
                        inputmode="numeric"
                        class="w-full tabular-nums"
                        required
                        :invalid="Boolean(form.errors.capacity)"
                        :aria-invalid="Boolean(form.errors.capacity)"
                        aria-describedby="room-type-capacity-error"
                    />
                    <InputError
                        id="room-type-capacity-error"
                        :message="form.errors.capacity"
                    />
                </div>
            </div>
        </section>

        <footer
            class="flex flex-col-reverse gap-3 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
        >
            <ActionLink
                :href="route('hotels.room-types.index', hotel.id)"
                variant="ghost"
            >
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
                          : t('room_types.actions.create')
                }}
            </PrimaryButton>
        </footer>
    </form>
</template>
