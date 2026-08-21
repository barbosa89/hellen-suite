<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: false,
});

defineProps({
    invalid: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const handleInput = (event) => {
    if (model.value !== undefined) {
        model.value = event.target.value;
    }
};
</script>

<template>
    <input
        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition-[border-color,box-shadow] duration-150 placeholder:text-neutral-600 focus:ring-2 focus:outline-hidden disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-600 motion-reduce:transition-none dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-400 dark:disabled:bg-neutral-900"
        :class="
            invalid
                ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 dark:border-danger-500'
                : 'border-neutral-300 dark:border-neutral-700'
        "
        v-bind="$attrs"
        :value="model"
        @input="handleInput"
        ref="input"
    />
</template>
