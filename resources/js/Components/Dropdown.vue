<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';

const dropdownGap = 8;
const viewportMargin = 8;

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1.5 bg-white dark:bg-neutral-900',
    },
});

const open = ref(false);
const positioned = ref(false);
const opensUpward = ref(false);
const trigger = ref(null);
const dropdown = ref(null);
const position = ref({
    left: 0,
    top: 0,
    maxHeight: 'calc(100vh - 1rem)',
});

let positionAnimationFrame = null;

const widthClass = computed(() => {
    return {
        48: 'w-48',
    }[props.width.toString()];
});

const transformOriginClasses = computed(() => {
    if (props.align === 'left') {
        return opensUpward.value
            ? 'ltr:origin-bottom-left rtl:origin-bottom-right'
            : 'ltr:origin-top-left rtl:origin-top-right';
    } else if (props.align === 'right') {
        return opensUpward.value
            ? 'ltr:origin-bottom-right rtl:origin-bottom-left'
            : 'ltr:origin-top-right rtl:origin-top-left';
    }

    return opensUpward.value ? 'origin-bottom' : 'origin-top';
});

const dropdownStyle = computed(() => ({
    left: `${position.value.left}px`,
    top: `${position.value.top}px`,
    maxHeight: position.value.maxHeight,
    visibility: positioned.value ? 'visible' : 'hidden',
}));

function constrain(value, minimum, maximum) {
    return Math.min(Math.max(value, minimum), Math.max(minimum, maximum));
}

function updatePosition() {
    if (!open.value || !trigger.value || !dropdown.value) {
        return;
    }

    const triggerBounds = trigger.value.getBoundingClientRect();
    const viewportWidth = document.documentElement.clientWidth;
    const viewportHeight = document.documentElement.clientHeight;
    const dropdownWidth = dropdown.value.offsetWidth;
    const naturalDropdownHeight = dropdown.value.scrollHeight;
    const availableBelow = Math.max(
        viewportHeight - triggerBounds.bottom - dropdownGap - viewportMargin,
        0,
    );
    const availableAbove = Math.max(
        triggerBounds.top - dropdownGap - viewportMargin,
        0,
    );

    opensUpward.value =
        naturalDropdownHeight > availableBelow &&
        availableAbove > availableBelow;

    const availableHeight = opensUpward.value ? availableAbove : availableBelow;
    const dropdownHeight = Math.min(naturalDropdownHeight, availableHeight);
    const isRightToLeft =
        window.getComputedStyle(trigger.value).direction === 'rtl';

    let left;

    if (props.align === 'left') {
        left = isRightToLeft
            ? triggerBounds.right - dropdownWidth
            : triggerBounds.left;
    } else if (props.align === 'right') {
        left = isRightToLeft
            ? triggerBounds.left
            : triggerBounds.right - dropdownWidth;
    } else {
        left = triggerBounds.left + (triggerBounds.width - dropdownWidth) / 2;
    }

    const top = opensUpward.value
        ? triggerBounds.top - dropdownGap - dropdownHeight
        : triggerBounds.bottom + dropdownGap;

    position.value = {
        left: constrain(
            left,
            viewportMargin,
            viewportWidth - dropdownWidth - viewportMargin,
        ),
        top: constrain(
            top,
            viewportMargin,
            viewportHeight - dropdownHeight - viewportMargin,
        ),
        maxHeight: `${availableHeight}px`,
    };
}

function schedulePositionUpdate() {
    if (positionAnimationFrame !== null) {
        return;
    }

    positionAnimationFrame = window.requestAnimationFrame(() => {
        positionAnimationFrame = null;
        updatePosition();
    });
}

function addPositionListeners() {
    window.addEventListener('resize', schedulePositionUpdate);
    window.addEventListener('scroll', schedulePositionUpdate, true);
}

function removePositionListeners() {
    window.removeEventListener('resize', schedulePositionUpdate);
    window.removeEventListener('scroll', schedulePositionUpdate, true);

    if (positionAnimationFrame !== null) {
        window.cancelAnimationFrame(positionAnimationFrame);
        positionAnimationFrame = null;
    }
}

async function toggleDropdown() {
    if (open.value) {
        closeDropdown();

        return;
    }

    positioned.value = false;
    open.value = true;

    await nextTick();

    if (!open.value) {
        return;
    }

    updatePosition();
    positioned.value = true;
    addPositionListeners();
}

function closeDropdown() {
    open.value = false;
    removePositionListeners();
}

function closeOnEscape(event) {
    if (open.value && event.key === 'Escape') {
        closeDropdown();
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    removePositionListeners();
});
</script>

<template>
    <div class="relative">
        <div ref="trigger" @click="toggleDropdown">
            <slot name="trigger" />
        </div>

        <Teleport to="body">
            <!-- Full Screen Dropdown Overlay -->
            <div
                v-show="open"
                class="fixed inset-0 z-40"
                @click="closeDropdown"
            ></div>

            <Transition
                enter-active-class="transition ease-out duration-200 motion-reduce:transition-none"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75 motion-reduce:transition-none"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-show="open"
                    ref="dropdown"
                    class="fixed z-50 max-w-[calc(100vw-1rem)] overflow-y-auto rounded-xl shadow-xl"
                    :class="[widthClass, transformOriginClasses]"
                    :style="dropdownStyle"
                    @click="closeDropdown"
                >
                    <div
                        class="overflow-hidden rounded-xl ring-1 ring-neutral-900/10 dark:ring-white/10"
                        :class="contentClasses"
                    >
                        <slot name="content" />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
