<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import HotelAvatar from '@/Components/HotelAvatar.vue';
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    RectangleGroupIcon,
    Squares2X2Icon,
    UserGroupIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
    mobileOpen: {
        type: Boolean,
        required: true,
    },
    collapsed: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['close', 'toggle-collapsed']);

const { t } = useI18n();
const sidebarElement = ref(null);
const mobileCloseButton = ref(null);
const isDesktop = ref(true);
let desktopMediaQuery;

const navigation = computed(() => [
    {
        name: t('app.navigation.overview'),
        href: route('hotels.management.index', props.hotel.id),
        active: route().current('hotels.management.*'),
        icon: Squares2X2Icon,
    },
    {
        name: t('rooms.title'),
        href: route('hotels.rooms.index', props.hotel.id),
        active: route().current('hotels.rooms.*'),
        icon: RectangleGroupIcon,
    },
    {
        name: t('guests.title'),
        href: route('hotels.guests.index', props.hotel.id),
        active: route().current('hotels.guests.*'),
        icon: UserGroupIcon,
    },
    {
        name: t('stays.title'),
        href: route('hotels.stays.index', props.hotel.id),
        active: route().current('hotels.stays.*'),
        icon: CalendarDaysIcon,
    },
]);

function updateDesktopState(event) {
    isDesktop.value = event.matches;
}

function handleKeydown(event) {
    if (isDesktop.value || !props.mobileOpen) {
        return;
    }

    if (event.key === 'Escape') {
        event.preventDefault();
        emit('close');
        return;
    }

    if (event.key !== 'Tab') {
        return;
    }

    const focusableElements = sidebarElement.value?.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
    );

    if (!focusableElements?.length) {
        return;
    }

    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault();
        lastElement.focus();
    } else if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault();
        firstElement.focus();
    }
}

onMounted(() => {
    desktopMediaQuery = window.matchMedia('(min-width: 64rem)');
    isDesktop.value = desktopMediaQuery.matches;
    desktopMediaQuery.addEventListener('change', updateDesktopState);
});

onUnmounted(() => {
    desktopMediaQuery?.removeEventListener('change', updateDesktopState);
});

defineExpose({
    focus: () => mobileCloseButton.value?.focus(),
});
</script>

<template>
    <aside
        ref="sidebarElement"
        class="fixed inset-y-0 inset-s-0 z-50 flex w-[17rem] flex-col border-e border-neutral-200 bg-white transition-[width,translate] duration-200 ease-out motion-reduce:transition-none lg:z-40 dark:border-neutral-800 dark:bg-neutral-950"
        :class="[
            mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            collapsed ? 'lg:w-20' : 'lg:w-[17rem]',
        ]"
        :aria-label="t('app.navigation.hotel_navigation')"
        :aria-hidden="!mobileOpen && !isDesktop ? 'true' : undefined"
        :aria-modal="!isDesktop && mobileOpen ? 'true' : undefined"
        :inert="!mobileOpen && !isDesktop"
        :role="!isDesktop ? 'dialog' : undefined"
        @keydown="handleKeydown"
    >
        <div
            class="flex h-16 shrink-0 items-center gap-3 border-b border-neutral-200 px-4 dark:border-neutral-800"
        >
            <Link
                :href="route('hotels.index')"
                class="focus-visible:ring-primary-500 flex min-w-0 flex-1 items-center gap-3 rounded-lg focus-visible:ring-2 focus-visible:outline-hidden"
            >
                <ApplicationLogo
                    class="text-primary-600 block h-8 w-8 shrink-0 fill-current"
                />
                <span
                    class="truncate text-lg font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white"
                    :class="collapsed ? 'lg:hidden' : ''"
                >
                    Hellen
                    <span class="text-primary-600 dark:text-primary-400"
                        >Suite</span
                    >
                </span>
            </Link>

            <button
                ref="mobileCloseButton"
                type="button"
                class="focus-visible:ring-primary-500 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden lg:hidden dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                :aria-label="t('app.navigation.close_menu')"
                @click="$emit('close')"
            >
                <XMarkIcon class="h-5 w-5" />
            </button>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-y-auto px-3 py-4">
            <Link
                :href="route('hotels.index')"
                prefetch
                class="group focus-visible:ring-primary-500 mb-4 flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-semibold text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                :title="collapsed ? t('app.navigation.all_hotels') : undefined"
                @click="$emit('close')"
            >
                <ArrowLeftIcon class="h-5 w-5 shrink-0" />
                <span :class="collapsed ? 'lg:hidden' : ''">{{
                    t('app.navigation.all_hotels')
                }}</span>
            </Link>

            <div
                class="mb-5 flex items-center gap-3 rounded-xl bg-neutral-100 p-3 dark:bg-neutral-900"
                :class="collapsed ? 'lg:justify-center lg:px-2' : ''"
            >
                <HotelAvatar :hotel="hotel" size="md" />
                <div class="min-w-0" :class="collapsed ? 'lg:hidden' : ''">
                    <p
                        class="truncate text-sm font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ hotel.business_name }}
                    </p>
                    <p
                        class="mt-0.5 truncate text-xs text-neutral-600 dark:text-neutral-400"
                    >
                        {{ hotel.tin }}
                    </p>
                </div>
            </div>

            <p
                class="mb-2 px-3 text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                :class="collapsed ? 'lg:sr-only' : ''"
            >
                {{ t('app.navigation.modules') }}
            </p>

            <nav class="grid gap-1">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    prefetch
                    class="group focus-visible:ring-primary-500 flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-semibold transition-colors focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none"
                    :class="[
                        item.active
                            ? 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300'
                            : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white',
                        collapsed ? 'lg:justify-center lg:px-2' : '',
                    ]"
                    :title="collapsed ? item.name : undefined"
                    @click="$emit('close')"
                >
                    <component
                        :is="item.icon"
                        class="h-5 w-5 shrink-0"
                        :class="
                            item.active
                                ? 'text-primary-600 dark:text-primary-400'
                                : 'text-neutral-500'
                        "
                    />
                    <span :class="collapsed ? 'lg:hidden' : ''">{{
                        item.name
                    }}</span>
                </Link>
            </nav>
        </div>

        <div
            class="hidden border-t border-neutral-200 p-3 lg:block dark:border-neutral-800"
        >
            <button
                type="button"
                class="focus-visible:ring-primary-500 flex min-h-11 w-full items-center gap-3 rounded-lg px-3 text-sm font-semibold text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                :class="collapsed ? 'justify-center px-2' : ''"
                :aria-label="
                    collapsed
                        ? t('app.navigation.expand_menu')
                        : t('app.navigation.collapse_menu')
                "
                @click="$emit('toggle-collapsed')"
            >
                <ChevronDoubleRightIcon
                    v-if="collapsed"
                    class="h-5 w-5 shrink-0"
                />
                <ChevronDoubleLeftIcon v-else class="h-5 w-5 shrink-0" />
                <span v-if="!collapsed">{{
                    t('app.navigation.collapse_menu')
                }}</span>
            </button>
        </div>
    </aside>

    <Transition
        enter-active-class="transition-opacity duration-200 ease-out motion-reduce:transition-none"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150 ease-in motion-reduce:transition-none"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <button
            v-if="mobileOpen"
            type="button"
            class="fixed inset-0 z-40 bg-neutral-950/55 lg:hidden"
            :aria-label="t('app.navigation.close_menu')"
            @click="$emit('close')"
        />
    </Transition>
</template>
