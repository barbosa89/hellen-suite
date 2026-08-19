<script setup>
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <DefaultLayout>
        <template #header>
            <h2
                class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow-sm sm:rounded-lg sm:p-8 dark:bg-neutral-800"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow-sm sm:rounded-lg sm:p-8 dark:bg-neutral-800"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow-sm sm:rounded-lg sm:p-8 dark:bg-neutral-800"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
