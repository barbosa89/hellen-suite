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
        class="focus-visible:ring-primary-500 relative inline-flex h-11 w-16 shrink-0 items-center rounded-full border transition-[background-color,border-color,box-shadow] duration-150 ease-out focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden motion-reduce:transition-none sm:h-9 dark:focus-visible:ring-offset-neutral-950"
        :class="
            isDark
                ? 'border-neutral-700 bg-neutral-900 hover:border-neutral-600 hover:bg-neutral-800'
                : 'border-neutral-300 bg-neutral-200 hover:border-neutral-400 hover:bg-neutral-300'
        "
        @click="toggle"
    >
        <span
            class="pointer-events-none absolute inset-y-0 start-0 flex items-center transition-transform duration-200 ease-in-out motion-reduce:transition-none"
            :class="isDark ? 'translate-x-8' : 'translate-x-1'"
        >
            <span
                class="flex h-7 w-7 items-center justify-center rounded-full shadow-sm ring-1 transition-[background-color,color,box-shadow] duration-150 ease-out ring-inset motion-reduce:transition-none"
                :class="
                    isDark
                        ? 'bg-primary-300 ring-primary-200 text-neutral-950'
                        : 'bg-secondary-100 ring-secondary-300 text-neutral-800'
                "
            >
                <SunIcon v-if="!isDark" class="h-4 w-4" />
                <MoonIcon v-else class="h-4 w-4" />
            </span>
        </span>
    </button>
</template>
