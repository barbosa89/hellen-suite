<template>
    <div class="position-fixed context-menu" :style="{ top: y + 'px', left: x + 'px' }">
        <div class="context-menu-item" v-for="action in actions" :key="action.action" @click="emitAction(action.action)">
            {{ action.label }}
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, onMounted, onUnmounted } from 'vue'

const { actions, x, y } = defineProps({
    actions: {
        type: Array,
        required: true,
    },
    x: {
        type: Number,
        required: true,
    },
    y: {
        type: Number,
        required: true,
    },
})

const emit = defineEmits(['action-clicked', 'update:modelValue'])

const emitAction = (action) => {
    emit('action-clicked', action)
    emit('update:modelValue', false)
}

const handleClickOutside = (event) => {
    if (!event.target.classList.contains('context-menu-item')) {
        emit('update:modelValue', false)
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style scoped>
.context-menu {
    height: auto;
    z-index: 50;
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    min-width: 150px;
}

.context-menu div {
    padding: 10px;
    cursor: pointer;
}

.context-menu div:hover {
    background-color: #f0f0f0;
}
</style>