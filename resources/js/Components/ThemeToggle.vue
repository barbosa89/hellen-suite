<script setup>
import { onMounted, ref } from 'vue';
import { MoonIcon, SunIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const isDark = ref(false);

function applyTheme(dark) {
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    isDark.value = dark;
}

function toggle() {
    applyTheme(!isDark.value);
}

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});
</script>

<template>
    <button
        type="button"
        role="switch"
        :aria-checked="isDark"
        :aria-label="isDark ? t('app.theme.light') : t('app.theme.dark')"
        class="focus:ring-primary-500 relative inline-flex h-9 w-16 shrink-0 items-center rounded-full transition duration-150 ease-in-out focus:ring-2 focus:ring-offset-2 focus:outline-hidden dark:focus:ring-offset-neutral-950"
        :class="
            isDark
                ? 'bg-primary-600 dark:bg-primary-500'
                : 'bg-primary-200 dark:bg-primary-900'
        "
        @click="toggle"
    >
        <span
            class="pointer-events-none absolute inset-y-0 start-0 flex items-center transition-transform duration-200 ease-in-out"
            :class="isDark ? 'translate-x-8' : 'translate-x-1'"
        >
            <span
                class="flex h-7 w-7 items-center justify-center rounded-full bg-white shadow-sm transition duration-150 ease-in-out dark:bg-neutral-950"
            >
                <SunIcon v-if="!isDark" class="text-secondary-500 h-4 w-4" />
                <MoonIcon v-else class="text-primary-300 h-4 w-4" />
            </span>
        </span>
    </button>
</template>
