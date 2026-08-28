<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import Checkbox from '@/Components/Checkbox.vue';
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
    room: {
        type: Object,
        default: null,
    },
    roomTypes: {
        type: Array,
        required: true,
    },
    currency: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();
const isEditing = computed(() => props.mode === 'edit');
const form = useForm({
    room_type_id: props.room?.room_type_id ?? '',
    number: props.room?.number ?? '',
    floor: props.room?.floor ?? '',
    reference_price: props.room?.reference_price ?? '',
    housekeeping_status: props.room?.housekeeping_status ?? 'clean',
    is_active: props.room?.is_active ?? true,
});

function submit() {
    if (isEditing.value) {
        form.put(route('hotels.rooms.update', [props.hotel.id, props.room.id]));

        return;
    }

    form.post(route('hotels.rooms.store', props.hotel.id));
}
</script>

<template>
    <form
        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
        @submit.prevent="submit"
    >
        <div class="grid divide-y divide-neutral-200 dark:divide-neutral-800">
            <section class="p-5 sm:p-7 lg:p-8">
                <div class="mb-6">
                    <h2
                        class="text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('rooms.form.assignment.title') }}
                    </h2>
                    <p
                        class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('rooms.form.assignment.description') }}
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <div class="grid content-start gap-2">
                        <InputLabel
                            for="number"
                            :value="t('rooms.fields.number.label')"
                            required
                        />
                        <TextInput
                            id="number"
                            v-model="form.number"
                            name="number"
                            type="text"
                            class="w-full tabular-nums"
                            required
                            autofocus
                            autocomplete="off"
                            :invalid="Boolean(form.errors.number)"
                            :aria-invalid="Boolean(form.errors.number)"
                            aria-describedby="room-number-error"
                        />
                        <InputError
                            id="room-number-error"
                            :message="form.errors.number"
                        />
                    </div>

                    <div class="grid content-start gap-2">
                        <InputLabel
                            for="room_type_id"
                            :value="t('rooms.fields.room_type.label')"
                            required
                        />
                        <select
                            id="room_type_id"
                            v-model="form.room_type_id"
                            name="room_type_id"
                            required
                            class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] duration-150 focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                            :class="
                                form.errors.room_type_id
                                    ? 'border-danger-500'
                                    : 'border-neutral-300 dark:border-neutral-700'
                            "
                            :aria-invalid="Boolean(form.errors.room_type_id)"
                            aria-describedby="room-type-error"
                        >
                            <option value="" disabled>
                                {{ t('rooms.form.select_type') }}
                            </option>
                            <option
                                v-for="roomType in roomTypes"
                                :key="roomType.id"
                                :value="roomType.id"
                            >
                                {{ roomType.name }} ·
                                {{
                                    t('room_types.capacity', {
                                        count: roomType.capacity,
                                    })
                                }}
                            </option>
                        </select>
                        <InputError
                            id="room-type-error"
                            :message="form.errors.room_type_id"
                        />
                    </div>

                    <div class="grid content-start gap-2">
                        <InputLabel
                            for="floor"
                            :value="t('rooms.fields.floor.label')"
                        />
                        <TextInput
                            id="floor"
                            v-model="form.floor"
                            name="floor"
                            type="text"
                            class="w-full"
                            autocomplete="off"
                            :invalid="Boolean(form.errors.floor)"
                            :aria-invalid="Boolean(form.errors.floor)"
                            aria-describedby="floor-error"
                        />
                        <InputError
                            id="floor-error"
                            :message="form.errors.floor"
                        />
                    </div>
                </div>
            </section>

            <section class="p-5 sm:p-7 lg:p-8">
                <div class="mb-6">
                    <h2
                        class="text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('rooms.form.operation.title') }}
                    </h2>
                    <p
                        class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('rooms.form.operation.description') }}
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <div class="grid content-start gap-2">
                        <InputLabel
                            for="reference_price"
                            :value="t('rooms.fields.reference_price.label')"
                            required
                        />
                        <div class="relative">
                            <TextInput
                                id="reference_price"
                                v-model="form.reference_price"
                                name="reference_price"
                                type="number"
                                min="0.01"
                                step="0.01"
                                inputmode="decimal"
                                class="w-full pe-16 tabular-nums"
                                required
                                :invalid="Boolean(form.errors.reference_price)"
                                :aria-invalid="
                                    Boolean(form.errors.reference_price)
                                "
                                aria-describedby="reference-price-help reference-price-error"
                            />
                            <span
                                class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                            >
                                {{ currency }}
                            </span>
                        </div>
                        <p
                            id="reference-price-help"
                            class="text-xs leading-5 text-neutral-600 dark:text-neutral-400"
                        >
                            {{ t('rooms.form.reference_price_hint') }}
                        </p>
                        <InputError
                            id="reference-price-error"
                            :message="form.errors.reference_price"
                        />
                    </div>

                    <div class="grid content-start gap-2">
                        <InputLabel
                            for="housekeeping_status"
                            :value="t('rooms.fields.housekeeping_status.label')"
                            required
                        />
                        <select
                            id="housekeeping_status"
                            v-model="form.housekeeping_status"
                            name="housekeeping_status"
                            required
                            class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] duration-150 focus:ring-2 focus:outline-hidden dark:bg-neutral-950 dark:text-neutral-100"
                            :class="
                                form.errors.housekeeping_status
                                    ? 'border-danger-500'
                                    : 'border-neutral-300 dark:border-neutral-700'
                            "
                            :aria-invalid="
                                Boolean(form.errors.housekeeping_status)
                            "
                            aria-describedby="housekeeping-error"
                        >
                            <option value="clean">
                                {{ t('rooms.housekeeping.clean') }}
                            </option>
                            <option value="dirty">
                                {{ t('rooms.housekeeping.dirty') }}
                            </option>
                        </select>
                        <InputError
                            id="housekeeping-error"
                            :message="form.errors.housekeeping_status"
                        />
                    </div>

                    <div
                        class="flex min-h-11 items-center gap-3 self-start rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 dark:border-neutral-800 dark:bg-neutral-950/60"
                    >
                        <Checkbox v-model:checked="form.is_active" />
                        <div>
                            <InputLabel
                                :value="t('rooms.fields.is_active.label')"
                            />
                            <p
                                class="mt-0.5 text-xs leading-5 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('rooms.form.activity_hint') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer
            class="flex flex-col-reverse gap-3 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
        >
            <ActionLink
                :href="route('hotels.rooms.index', hotel.id)"
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
                          : t('rooms.actions.create')
                }}
            </PrimaryButton>
        </footer>
    </form>
</template>
