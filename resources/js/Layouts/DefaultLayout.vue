<script setup>
import HotelAvatar from '@/Components/HotelAvatar.vue';
import LocaleDropdown from '@/Components/LocaleDropdown.vue';
import Sidebar from '@/Components/Sidebar.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Bars3Icon, Cog6ToothIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
});

const mobileSidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const sidebar = ref(null);
const menuButton = ref(null);

onMounted(() => {
    sidebarCollapsed.value =
        localStorage.getItem('hotel-sidebar-collapsed') === 'true';
});

function toggleCollapsed() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem(
        'hotel-sidebar-collapsed',
        sidebarCollapsed.value.toString(),
    );
}

watch(mobileSidebarOpen, async (isOpen) => {
    await nextTick();

    if (isOpen) {
        sidebar.value?.focus();
    } else {
        menuButton.value?.focus();
    }
});
</script>

<template>
    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950">
        <Sidebar
            ref="sidebar"
            :hotel="props.hotel"
            :mobile-open="mobileSidebarOpen"
            :collapsed="sidebarCollapsed"
            @close="mobileSidebarOpen = false"
            @toggle-collapsed="toggleCollapsed"
        />

        <div
            class="min-h-screen transition-[padding] duration-200 ease-out motion-reduce:transition-none"
            :class="sidebarCollapsed ? 'lg:ps-20' : 'lg:ps-[17rem]'"
        >
            <nav
                class="sticky top-0 z-30 border-b border-neutral-200 bg-white/95 dark:border-neutral-800 dark:bg-neutral-950/95"
            >
                <div
                    class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8 2xl:px-10"
                >
                    <button
                        ref="menuButton"
                        type="button"
                        class="focus-visible:ring-primary-500 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none lg:hidden dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                        :aria-label="$t('app.navigation.open_menu')"
                        @click="mobileSidebarOpen = true"
                    >
                        <Bars3Icon class="h-5 w-5" />
                    </button>

                    <div class="flex min-w-0 items-center gap-3 lg:hidden">
                        <HotelAvatar :hotel="props.hotel" size="sm" />
                        <div class="min-w-0">
                            <p
                                class="truncate text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ props.hotel.business_name }}
                            </p>
                            <p
                                class="truncate text-xs text-neutral-600 dark:text-neutral-400"
                            >
                                {{ $t('hotels.selected_hotel') }}
                            </p>
                        </div>
                    </div>

                    <div class="ms-auto flex items-center gap-2">
                        <Link
                            :href="route('settings.edit')"
                            class="focus-visible:ring-primary-500 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-950 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden sm:h-9 sm:w-9 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white dark:focus-visible:ring-offset-neutral-950"
                            :aria-label="$t('settings.title')"
                        >
                            <Cog6ToothIcon class="h-5 w-5" />
                        </Link>
                        <ThemeToggle />
                        <LocaleDropdown />
                    </div>
                </div>
            </nav>

            <main class="min-h-[calc(100vh-4rem)]">
                <slot />
            </main>
        </div>
    </div>
</template>
