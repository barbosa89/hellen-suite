<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    entry: { type: Object, required: true },
    hotelId: { type: [String, Number], required: true },
    countries: { type: Array, default: () => [] },
    subdivisions: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
    prefix: { type: String, default: '' },
    idPrefix: { type: String, default: 'travel' },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:entry']);
const { t } = useI18n();

const field = (name) => (props.prefix ? `${props.prefix}.${name}` : name);
const error = (name) => computed(() => props.errors[field(name)]);
const entryValue = (name) =>
    computed({
        get: () => props.entry[name] ?? '',
        set: (value) => emit('update:entry', { ...props.entry, [name]: value }),
    });

const localityCache = ref({});
const loadingLocalities = ref(false);

function subdivisionsFor(country) {
    if (country !== 'COL') {
        return [];
    }

    return props.subdivisions;
}

function localitiesFor(kind) {
    return localityCache.value[props.entry[`${kind}_subdivision`]] ?? [];
}

async function loadLocalities(kind) {
    const subdivision = props.entry[`${kind}_subdivision`];

    if (!subdivision || localityCache.value[subdivision]) {
        return;
    }

    loadingLocalities.value = true;

    try {
        const parameters = new URLSearchParams({
            country: 'CO',
            subdivision,
        });
        const response = await fetch(
            `${route('hotels.jurisdiction-localities.index', props.hotelId)}?${parameters}`,
        );
        const data = response.ok ? await response.json() : { localities: [] };
        localityCache.value[subdivision] = data.localities ?? [];
    } catch {
        localityCache.value[subdivision] = [];
    } finally {
        loadingLocalities.value = false;
    }
}

function onCountryChange(kind) {
    emit('update:entry', {
        ...props.entry,
        [`${kind}_subdivision`]: '',
        [`${kind}_locality`]: '',
    });
}

function onSubdivisionChange(kind) {
    emit('update:entry', { ...props.entry, [`${kind}_locality`]: '' });
    loadLocalities(kind);
}

const groups = ['residence', 'origin', 'destination'];

groups.forEach((kind) => {
    watch(
        () => props.entry[`${kind}_subdivision`],
        (subdivision) => {
            if (subdivision) {
                loadLocalities(kind);
            }
        },
        { immediate: true },
    );
});

const selectClasses =
    'focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] focus:ring-2 focus:outline-hidden disabled:cursor-not-allowed disabled:opacity-60 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100';
</script>

<template>
    <div class="grid gap-5">
        <div
            v-for="kind in groups"
            :key="kind"
            class="grid gap-5 rounded-xl border border-neutral-200 p-4 md:grid-cols-3 dark:border-neutral-800"
        >
            <p
                class="text-sm font-semibold text-neutral-950 md:col-span-3 dark:text-white"
            >
                {{ t(`stays.form.travel.${kind}.title`) }}
            </p>
            <div class="grid content-start gap-2">
                <InputLabel
                    :for="`${idPrefix}-${kind}-country`"
                    :value="t('stays.form.travel.country.label')"
                    required
                />
                <select
                    :id="`${idPrefix}-${kind}-country`"
                    :value="entry[`${kind}_country`] ?? ''"
                    :disabled="disabled"
                    :class="selectClasses"
                    @change="
                        $emit('update:entry', {
                            ...entry,
                            [`${kind}_country`]: $event.target.value,
                        });
                        onCountryChange(kind);
                    "
                >
                    <option value="">
                        {{ t('app.not_provided') }}
                    </option>
                    <option
                        v-for="country in countries"
                        :key="country.code"
                        :value="country.code"
                    >
                        {{ country.code }} — {{ country.name }}
                    </option>
                </select>
                <InputError :message="error(`${kind}_country`).value" />
            </div>
            <div class="grid content-start gap-2">
                <InputLabel
                    :for="`${idPrefix}-${kind}-subdivision`"
                    :value="t('stays.form.travel.subdivision.label')"
                />
                <select
                    :id="`${idPrefix}-${kind}-subdivision`"
                    :value="entry[`${kind}_subdivision`] ?? ''"
                    :disabled="
                        disabled ||
                        !subdivisionsFor(entry[`${kind}_country`]).length
                    "
                    :class="selectClasses"
                    @change="
                        $emit('update:entry', {
                            ...entry,
                            [`${kind}_subdivision`]: $event.target.value,
                        });
                        onSubdivisionChange(kind);
                    "
                >
                    <option value="">
                        {{ t('app.not_provided') }}
                    </option>
                    <option
                        v-for="subdivision in subdivisionsFor(
                            entry[`${kind}_country`],
                        )"
                        :key="subdivision.code"
                        :value="subdivision.code"
                    >
                        {{ subdivision.code }} — {{ subdivision.name }}
                    </option>
                </select>
                <InputError :message="error(`${kind}_subdivision`).value" />
            </div>
            <div class="grid content-start gap-2">
                <InputLabel
                    :for="`${idPrefix}-${kind}-locality`"
                    :value="t('stays.form.travel.locality.label')"
                />
                <select
                    :id="`${idPrefix}-${kind}-locality`"
                    :value="entry[`${kind}_locality`] ?? ''"
                    :disabled="disabled || !localitiesFor(kind).length"
                    :class="selectClasses"
                    @change="
                        $emit('update:entry', {
                            ...entry,
                            [`${kind}_locality`]: $event.target.value,
                        })
                    "
                >
                    <option value="">
                        {{
                            loadingLocalities
                                ? t('stays.form.travel.loading_localities')
                                : t('app.not_provided')
                        }}
                    </option>
                    <option
                        v-for="locality in localitiesFor(kind)"
                        :key="locality.code"
                        :value="locality.code"
                    >
                        {{ locality.code }} — {{ locality.name }}
                    </option>
                </select>
                <InputError :message="error(`${kind}_locality`).value" />
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="grid content-start gap-2">
                <InputLabel
                    :for="`${idPrefix}-purpose`"
                    :value="t('stays.form.travel.purpose.label')"
                    required
                />
                <TextInput
                    :id="`${idPrefix}-purpose`"
                    v-model="entryValue('travel_purpose').value"
                    class="w-full tabular-nums"
                    maxlength="2"
                    inputmode="numeric"
                    autocomplete="off"
                    :disabled="disabled"
                    :invalid="Boolean(error('travel_purpose').value)"
                />
                <InputError :message="error('travel_purpose').value" />
            </div>
            <div class="grid content-start gap-2">
                <InputLabel
                    :for="`${idPrefix}-transport`"
                    :value="t('stays.form.travel.transport.label')"
                    required
                />
                <TextInput
                    :id="`${idPrefix}-transport`"
                    v-model="entryValue('transport_means').value"
                    class="w-full tabular-nums"
                    maxlength="2"
                    inputmode="numeric"
                    autocomplete="off"
                    :disabled="disabled"
                    :invalid="Boolean(error('transport_means').value)"
                />
                <InputError :message="error('transport_means').value" />
            </div>
        </div>
    </div>
</template>
