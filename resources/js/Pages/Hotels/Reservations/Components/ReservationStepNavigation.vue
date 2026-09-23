<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    steps: { type: Array, required: true },
    currentStep: { type: Number, required: true },
});

const emit = defineEmits(['select']);
const { t } = useI18n();

function selectStep(step) {
    if (step < props.currentStep) {
        emit('select', step);
    }
}
</script>

<template>
    <nav
        class="rounded-2xl bg-white p-2 shadow-sm dark:bg-neutral-900"
        :aria-label="t('reservations.form.steps.label')"
    >
        <ol class="grid gap-1 sm:grid-cols-4">
            <li v-for="(step, index) in steps" :key="step">
                <button
                    type="button"
                    class="focus-visible:ring-primary-500 flex min-h-12 w-full items-center gap-3 rounded-xl px-3 text-start text-sm font-semibold focus-visible:ring-2 focus-visible:outline-hidden disabled:cursor-default"
                    :class="
                        currentStep === index + 1
                            ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                            : index + 1 < currentStep
                              ? 'text-neutral-800 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800'
                              : 'text-neutral-500'
                    "
                    :disabled="index + 1 > currentStep"
                    :aria-current="
                        currentStep === index + 1 ? 'step' : undefined
                    "
                    @click="selectStep(index + 1)"
                >
                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs tabular-nums dark:bg-neutral-800"
                        >{{ index + 1 }}</span
                    >{{ step }}
                </button>
            </li>
        </ol>
    </nav>
</template>
