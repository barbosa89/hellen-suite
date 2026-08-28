<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: { type: Object, required: true },
    excludedGuestIds: { type: Array, default: () => [] },
    id: { type: String, required: true },
});
const emit = defineEmits(['select']);
const { t } = useI18n();
const searchTerm = ref('');
const searchResults = ref([]);
const searching = ref(false);
let searchTimer;

function searchGuests() {
    clearTimeout(searchTimer);

    const term = searchTerm.value.trim();

    if (term.length < 2) {
        searchResults.value = [];

        return;
    }

    searchTimer = setTimeout(async () => {
        searching.value = true;

        try {
            const response = await fetch(
                `${route('hotels.guests.lookup', props.hotel.id)}?search=${encodeURIComponent(term)}`,
            );
            const data = response.ok ? await response.json() : { guests: [] };

            searchResults.value = (data.guests ?? []).filter(
                (guest) => !props.excludedGuestIds.includes(guest.id),
            );
        } finally {
            searching.value = false;
        }
    }, 250);
}

function selectGuest(guest) {
    searchTerm.value = '';
    searchResults.value = [];
    emit('select', guest);
}

onUnmounted(() => clearTimeout(searchTimer));
</script>

<template>
    <div class="relative">
        <InputLabel :for="id" :value="t('stays.form.guests.search')" />
        <div class="relative mt-2">
            <MagnifyingGlassIcon
                class="absolute inset-s-3 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-500"
            />
            <TextInput
                :id="id"
                v-model="searchTerm"
                class="w-full ps-10"
                autocomplete="off"
                @input="searchGuests"
            />
        </div>
        <p
            v-if="searching"
            class="mt-2 text-xs text-neutral-600 dark:text-neutral-400"
        >
            {{ t('app.searching') }}
        </p>
        <ul
            v-if="searchResults.length"
            class="absolute z-10 mt-2 max-h-52 w-full overflow-y-auto rounded-xl border border-neutral-200 bg-white p-1 shadow-lg dark:border-neutral-700 dark:bg-neutral-900"
        >
            <li v-for="result in searchResults" :key="result.id">
                <button
                    type="button"
                    class="focus-visible:ring-primary-500 w-full rounded-lg px-3 py-2 text-start hover:bg-neutral-100 focus-visible:ring-2 focus-visible:outline-hidden dark:hover:bg-neutral-800"
                    @click="selectGuest(result)"
                >
                    <span
                        class="block font-semibold text-neutral-950 dark:text-white"
                        >{{ result.first_name }} {{ result.last_name }}</span
                    >
                    <span class="text-xs text-neutral-600 dark:text-neutral-400"
                        >{{ result.identification_type.code }} ·
                        {{ result.identification_number }}</span
                    >
                </button>
            </li>
        </ul>
    </div>
</template>
