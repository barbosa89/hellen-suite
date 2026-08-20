<script setup>
import { computed, ref, useAttrs } from 'vue';
import { ArrowUpTrayIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const model = defineModel({
    type: [File, Array],
    default: null,
});

const props = defineProps({
    multiple: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);
const dragging = ref(false);

defineExpose({ focus: () => input.value?.click() });

const innerAccept = computed(() => {
    const { accept } = useAttrs();
    return accept ?? undefined;
});

const files = computed(() =>
    props.multiple
        ? Array.isArray(model.value)
            ? model.value
            : []
        : model.value
          ? [model.value]
          : [],
);

function handleFileChange(event) {
    const selected = Array.from(event.target.files ?? []);
    if (selected.length === 0) {
        return;
    }

    model.value = props.multiple ? selected : selected[0];

    event.target.value = '';
}

function removeFile(index) {
    if (props.multiple) {
        const next = [...files.value];
        next.splice(index, 1);
        model.value = next;
    } else {
        model.value = null;
    }
}

function onDrop(event) {
    dragging.value = false;
    const dropped = Array.from(event.dataTransfer?.files ?? []);
    if (dropped.length === 0) {
        return;
    }

    model.value = props.multiple ? dropped : dropped[0];
}
</script>

<template>
    <div class="space-y-2">
        <div
            class="focus-within:border-primary-500 focus-within:ring-primary-500 dark:focus-within:border-primary-500 dark:focus-within:ring-primary-500 flex flex-wrap items-center justify-between gap-3 rounded-md border-2 border-dashed border-neutral-300 px-4 py-3 transition duration-150 ease-in-out dark:border-neutral-700 dark:bg-neutral-900"
            :class="
                dragging
                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-400/10'
                    : ''
            "
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="onDrop"
        >
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-300 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md"
                >
                    <ArrowUpTrayIcon class="h-5 w-5" />
                </span>

                <div>
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-neutral-100"
                    >
                        <slot name="hint">
                            {{
                                files.length === 0
                                    ? t('app.upload.select')
                                    : t(
                                          'app.upload.files_selected',
                                          files.length,
                                      )
                            }}
                        </slot>
                    </p>
                    <span
                        v-if="files.length === 0"
                        class="text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        {{ innerAccept ?? 'PDF, PNG, JPG' }} ·
                        {{ t('app.upload.drop_hint') }}
                    </span>
                </div>
            </div>

            <input
                ref="input"
                type="file"
                :id="$attrs?.id"
                class="sr-only"
                :accept="innerAccept"
                :multiple="multiple"
                v-bind="$attrs"
                aria-hidden="true"
                tabindex="-1"
                @change="handleFileChange"
            />

            <button
                type="button"
                class="hover:bg-primary-800 focus:bg-primary-800 focus:ring-primary-500 dark:bg-primary-400 dark:hover:bg-primary-500 dark:focus:bg-primary-500 dark:active:bg-primary-600 bg-primary-500 active:bg-primary-900 inline-flex items-center rounded-md px-3 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out focus:ring-2 focus:ring-offset-2 focus:outline-hidden dark:text-neutral-950 dark:focus:ring-offset-neutral-950"
                @click="input?.click()"
            >
                {{ t('app.upload.select') }}
            </button>
        </div>

        <ul
            v-if="files.length > 0"
            class="flex flex-wrap gap-2"
            aria-live="polite"
        >
            <li
                v-for="(file, index) in files"
                :key="`${file.name}-${index}`"
                class="flex items-center gap-2 rounded-md border border-neutral-200 bg-white px-3 py-1.5 text-sm text-neutral-700 shadow-xs dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
            >
                <span class="max-w-56 truncate">{{ file.name }}</span>
                <button
                    type="button"
                    class="focus:ring-primary-500 hover:text-danger-600 dark:hover:text-danger-400 text-neutral-400 transition duration-150 ease-in-out focus:ring-2 focus:outline-hidden dark:text-neutral-500"
                    :aria-label="`Quitar ${file.name}`"
                    @click="removeFile(index)"
                >
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </li>
        </ul>
    </div>
</template>
