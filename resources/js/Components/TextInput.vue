<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: false,
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
        class="focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-500 dark:focus:ring-primary-500 rounded-md border-neutral-300 shadow-xs dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
        v-bind="$attrs"
        :value="model"
        @input="handleInput"
        ref="input"
    />
</template>
