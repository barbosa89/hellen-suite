<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import DangerButton from '@/Components/DangerButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Modal from '@/Components/Modal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import HotelLayout from '@/Layouts/HotelLayout.vue';
import {
    ArrowDownTrayIcon,
    ArrowPathIcon,
    ArrowUpTrayIcon,
    ExclamationTriangleIcon,
    LockClosedIcon,
} from '@heroicons/vue/24/outline';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    selectedBackup: { type: Object, default: null },
    pendingExport: { type: String, default: null },
    flash: { type: Object, default: () => ({}) },
});

const { t } = useI18n();

const exportForm = useForm({});
const restoreForm = useForm({});
const confirmVisible = ref(false);
const overwriteVisible = ref(false);

const hasSelection = computed(() => Boolean(props.selectedBackup?.path));
const hasPendingExport = computed(() => Boolean(props.pendingExport));

const createdAt = computed(() => {
    const value = props.selectedBackup?.created_at;

    if (!value) {
        return '';
    }

    return new Date(value).toLocaleString();
});

const size = computed(() => {
    const bytes = props.selectedBackup?.size ?? 0;

    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1048576) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / 1048576).toFixed(1)} MB`;
});

function createBackup() {
    exportForm.post(route('settings.backup.export'));
}

function confirmOverwrite() {
    overwriteVisible.value = false;
    exportForm.post(route('settings.backup.export'), { overwrite: true });
}

function chooseBackup() {
    restoreForm.post(route('settings.backup.restore.select'));
}

function openConfirmation() {
    confirmVisible.value = true;
}

function closeConfirmation() {
    confirmVisible.value = false;
}

function confirmRestore() {
    restoreForm.post(route('settings.backup.restore.confirm'), {
        onFinish: () => {
            confirmVisible.value = false;
        },
    });
}

function cancelRestore() {
    confirmVisible.value = false;
    router.post(route('settings.backup.restore.cancel'));
}
</script>

<template>
    <HotelLayout>
        <div class="w-full px-4 py-8 sm:px-6 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('backup.title')"
                    :description="t('backup.description')"
                >
                    <template #actions>
                        <ActionLink
                            :href="route('settings.edit')"
                            variant="ghost"
                        >
                            {{ t('settings.title') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <FlashMessage v-if="flash.success" :message="flash.success" />
                <FlashMessage
                    v-if="flash.error"
                    :message="flash.error"
                    variant="error"
                />

                <div class="grid gap-6 xl:grid-cols-2">
                    <section
                        class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    >
                        <div class="flex flex-1 flex-col p-5 sm:p-7">
                            <span
                                class="bg-primary-50 text-primary-700 dark:bg-primary-900/25 dark:text-primary-300 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                            >
                                <ArrowUpTrayIcon class="h-5 w-5" />
                            </span>
                            <h2
                                class="mt-4 text-lg font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('backup.sections.export.heading') }}
                            </h2>
                            <p
                                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('backup.sections.export.description') }}
                            </p>
                            <p
                                class="mt-4 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('backup.sections.export.hint') }}
                            </p>

                            <div
                                v-if="hasPendingExport"
                                class="bg-danger-50 text-danger-900 ring-danger-200 dark:bg-danger-900/25 dark:text-danger-300 dark:ring-danger-800 mt-5 flex items-start gap-3 rounded-xl px-4 py-3 text-sm leading-6 ring-1"
                            >
                                <ExclamationTriangleIcon
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                />
                                <p>
                                    {{
                                        t(
                                            'backup.sections.export.overwrite_warning',
                                            {
                                                path: pendingExport,
                                            },
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="bg-secondary-50 text-secondary-900 ring-secondary-200 dark:bg-secondary-900/20 dark:text-secondary-200 dark:ring-secondary-800 mt-5 flex items-start gap-3 rounded-xl px-4 py-3 text-sm leading-6 ring-1"
                            >
                                <LockClosedIcon
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                />
                                <p>{{ t('backup.warnings.plain') }}</p>
                            </div>
                            <p
                                class="mt-3 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('backup.warnings.storage') }}
                            </p>
                        </div>

                        <footer
                            class="flex justify-end border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
                        >
                            <PrimaryButton
                                type="button"
                                :disabled="exportForm.processing"
                                @click="createBackup"
                            >
                                <ArrowUpTrayIcon class="h-4 w-4" />
                                {{
                                    exportForm.processing
                                        ? t('backup.sections.export.working')
                                        : t('backup.sections.export.action')
                                }}
                            </PrimaryButton>
                            <PrimaryButton
                                v-if="hasPendingExport"
                                type="button"
                                :disabled="exportForm.processing"
                                @click="confirmOverwrite"
                            >
                                {{
                                    t('backup.sections.export.overwrite_action')
                                }}
                            </PrimaryButton>
                        </footer>
                    </section>

                    <section
                        class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                    >
                        <div class="flex flex-1 flex-col p-5 sm:p-7">
                            <span
                                class="bg-danger-50 text-danger-700 dark:bg-danger-900/25 dark:text-danger-300 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                            >
                                <ArrowDownTrayIcon class="h-5 w-5" />
                            </span>
                            <h2
                                class="mt-4 text-lg font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('backup.sections.restore.heading') }}
                            </h2>
                            <p
                                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('backup.sections.restore.description') }}
                            </p>
                            <p
                                class="mt-4 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('backup.sections.restore.hint') }}
                            </p>

                            <div
                                v-if="hasSelection"
                                class="mt-5 overflow-hidden rounded-xl ring-1 ring-neutral-200 dark:ring-neutral-800"
                            >
                                <dl
                                    class="divide-y divide-neutral-200 text-sm dark:divide-neutral-800"
                                >
                                    <div
                                        class="flex items-baseline justify-between gap-4 px-4 py-3"
                                    >
                                        <dt
                                            class="text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{ t('backup.details.file') }}
                                        </dt>
                                        <dd
                                            class="truncate text-right font-medium text-neutral-950 dark:text-white"
                                        >
                                            {{ selectedBackup.path }}
                                        </dd>
                                    </div>
                                    <div
                                        class="flex items-baseline justify-between gap-4 px-4 py-3"
                                    >
                                        <dt
                                            class="text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{ t('backup.details.created_at') }}
                                        </dt>
                                        <dd
                                            class="text-right font-medium text-neutral-950 dark:text-white"
                                        >
                                            {{ createdAt }}
                                        </dd>
                                    </div>
                                    <div
                                        class="flex items-baseline justify-between gap-4 px-4 py-3"
                                    >
                                        <dt
                                            class="text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{
                                                t(
                                                    'backup.details.application_version',
                                                )
                                            }}
                                        </dt>
                                        <dd
                                            class="text-right font-medium text-neutral-950 dark:text-white"
                                        >
                                            {{
                                                selectedBackup.application_version
                                            }}
                                        </dd>
                                    </div>
                                    <div
                                        class="flex items-baseline justify-between gap-4 px-4 py-3"
                                    >
                                        <dt
                                            class="text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{ t('backup.details.files') }}
                                        </dt>
                                        <dd
                                            class="text-right font-medium text-neutral-950 dark:text-white"
                                        >
                                            {{ selectedBackup.files }}
                                        </dd>
                                    </div>
                                    <div
                                        class="flex items-baseline justify-between gap-4 px-4 py-3"
                                    >
                                        <dt
                                            class="text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{ t('backup.details.size') }}
                                        </dt>
                                        <dd
                                            class="text-right font-medium text-neutral-950 dark:text-white"
                                        >
                                            {{ size }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div
                                class="bg-secondary-50 text-secondary-900 ring-secondary-200 dark:bg-secondary-900/20 dark:text-secondary-200 dark:ring-secondary-800 mt-5 flex items-start gap-3 rounded-xl px-4 py-3 text-sm leading-6 ring-1"
                            >
                                <LockClosedIcon
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                />
                                <p>{{ t('backup.warnings.keys') }}</p>
                            </div>
                        </div>

                        <footer
                            class="flex flex-wrap justify-end gap-3 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
                        >
                            <SecondaryButton
                                type="button"
                                :disabled="restoreForm.processing"
                                @click="chooseBackup"
                            >
                                <ArrowPathIcon class="h-4 w-4" />
                                {{
                                    restoreForm.processing
                                        ? t('backup.sections.restore.working')
                                        : t('backup.sections.restore.action')
                                }}
                            </SecondaryButton>
                            <DangerButton
                                v-if="hasSelection"
                                type="button"
                                :disabled="restoreForm.processing"
                                @click="openConfirmation"
                            >
                                {{
                                    t('backup.sections.restore.confirm_action')
                                }}
                            </DangerButton>
                        </footer>
                    </section>
                </div>
            </div>
        </div>

        <Modal :show="confirmVisible" max-width="lg" @close="closeConfirmation">
            <div class="p-5 sm:p-7">
                <span
                    class="bg-danger-50 text-danger-700 dark:bg-danger-900/25 dark:text-danger-300 flex h-11 w-11 items-center justify-center rounded-xl"
                >
                    <ExclamationTriangleIcon class="h-5 w-5" />
                </span>
                <h2
                    class="mt-4 text-lg font-semibold text-neutral-950 dark:text-white"
                >
                    {{ t('backup.sections.restore.confirm_heading') }}
                </h2>
                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{ t('backup.sections.restore.confirm_description') }}
                </p>
                <p
                    class="mt-4 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                >
                    {{ t('backup.sections.restore.hint') }}
                </p>
            </div>
            <div
                class="flex flex-col-reverse gap-3 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7 dark:border-neutral-800 dark:bg-neutral-950/60"
            >
                <SecondaryButton
                    type="button"
                    :disabled="restoreForm.processing"
                    @click="cancelRestore"
                >
                    {{ t('backup.sections.restore.cancel') }}
                </SecondaryButton>
                <DangerButton
                    type="button"
                    :disabled="restoreForm.processing"
                    @click="confirmRestore"
                >
                    {{ t('backup.sections.restore.confirm_action') }}
                </DangerButton>
            </div>
        </Modal>
    </HotelLayout>
</template>
