<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-danger-600 dark:text-danger-400 text-sm">
            {{ message }}
        </p>
    </div>
</template>
