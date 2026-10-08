<script setup>
import { computed, ref, useAttrs } from 'vue';
import {
    ArrowUpTrayIcon,
    PhotoIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

defineOptions({ inheritAttrs: false });

const { t } = useI18n();
const attrs = useAttrs();

const model = defineModel({
    type: [File, Array],
    default: null,
});

const props = defineProps({
    multiple: {
        type: Boolean,
        default: false,
    },
    invalid: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: null,
    },
    hint: {
        type: String,
        default: null,
    },
});

const input = ref(null);
const dragging = ref(false);

defineExpose({ focus: () => input.value?.click() });

const acceptedFormats = computed(() => attrs.accept ?? undefined);
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
        const nextFiles = [...files.value];
        nextFiles.splice(index, 1);
        model.value = nextFiles;

        return;
    }

    model.value = null;
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
    <div class="grid gap-3">
        <div
            class="group focus-within:ring-primary-500/20 flex min-h-36 flex-col items-center justify-center gap-4 rounded-xl border border-dashed px-5 py-6 text-center transition-[background-color,border-color,box-shadow] duration-150 ease-out focus-within:ring-2 motion-reduce:transition-none"
            :class="[
                dragging
                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-950/40'
                    : 'bg-neutral-50 hover:bg-white dark:bg-neutral-950 dark:hover:bg-neutral-900',
                invalid
                    ? 'border-danger-500'
                    : 'border-neutral-300 dark:border-neutral-700',
            ]"
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="onDrop"
        >
            <span
                class="text-primary-600 dark:text-primary-400 flex h-11 w-11 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-neutral-200 dark:bg-neutral-900 dark:ring-neutral-700"
            >
                <PhotoIcon v-if="files.length === 0" class="h-5 w-5" />
                <ArrowUpTrayIcon v-else class="h-5 w-5" />
            </span>

            <div class="grid gap-1">
                <p
                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100"
                >
                    {{
                        files.length === 0
                            ? (title ?? t('app.upload.drop_title'))
                            : t('app.upload.files_selected', files.length)
                    }}
                </p>
                <p
                    class="text-xs leading-5 text-neutral-600 dark:text-neutral-400"
                >
                    {{ hint ?? t('app.upload.drop_hint') }}
                </p>
            </div>

            <input
                ref="input"
                type="file"
                :id="attrs.id"
                class="sr-only"
                :accept="acceptedFormats"
                :multiple="multiple"
                v-bind="attrs"
                @change="handleFileChange"
            />

            <button
                type="button"
                class="hover:border-primary-300 hover:text-primary-700 focus-visible:ring-primary-500 dark:hover:border-primary-700 dark:hover:text-primary-300 inline-flex min-h-11 items-center justify-center rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-sm font-semibold text-neutral-700 shadow-sm transition-colors duration-150 focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none sm:min-h-9 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                @click="input?.click()"
            >
                {{ t('app.upload.select') }}
            </button>
        </div>

        <ul v-if="files.length > 0" class="grid gap-2" aria-live="polite">
            <li
                v-for="(file, index) in files"
                :key="`${file.name}-${index}`"
                class="flex items-center justify-between gap-3 rounded-lg bg-neutral-100 px-3 py-2 text-sm text-neutral-700 dark:bg-neutral-900 dark:text-neutral-300"
            >
                <span class="min-w-0 truncate">{{ file.name }}</span>
                <button
                    type="button"
                    class="hover:text-danger-600 focus-visible:ring-primary-500 dark:hover:text-danger-400 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-neutral-600 transition-colors hover:bg-white focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none sm:h-9 sm:w-9 dark:text-neutral-400 dark:hover:bg-neutral-800"
                    :aria-label="t('app.upload.remove', { name: file.name })"
                    @click="removeFile(index)"
                >
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </li>
        </ul>
    </div>
</template>
