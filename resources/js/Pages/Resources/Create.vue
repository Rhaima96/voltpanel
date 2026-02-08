<template>
    <PanelLayout>
        <!-- Header -->
        <div class="mb-6">
            <Link
                :href="route('voltpanel.resources.index', { resource })"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-2 inline-block"
            >
                ← Back to list
            </Link>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ title }}
            </h1>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-12 gap-6">
                    <template v-for="(component, index) in form.components" :key="index">
                        <!-- Section Component -->
                        <div v-if="component.type === 'section'" class="col-span-12 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div v-if="component.heading" class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ component.heading }}
                                </h3>
                                <p v-if="component.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ component.description }}
                                </p>
                            </div>
                            <div class="grid grid-cols-12 gap-6">
                                <FormField
                                    v-for="field in component.components"
                                    :key="field.name"
                                    :component="field"
                                    v-model="formData[field.name]"
                                />
                            </div>
                        </div>
                        <!-- Regular Field Component -->
                        <FormField
                            v-else
                            :component="component"
                            v-model="formData[component.name]"
                        />
                    </template>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <Link
                        :href="route('voltpanel.resources.index', { resource })"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-all focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :disabled="processing"
                    >
                        {{ processing ? 'Creating...' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </PanelLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PanelLayout from '../../../layouts/VoltPanel/PanelLayout.vue';
import FormField from '../../../components/VoltPanel/FormField.vue';
import { useRouting } from '../../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const props = defineProps({
    resource: String,
    title: String,
    form: Object,
});

const formData = ref({});
const processing = ref(false);

const submit = () => {
    processing.value = true;

    // Check if we have any file uploads
    const hasFiles = Object.values(formData.value).some(value => value instanceof File);

    const form = useForm(formData.value);

    form.post(route('voltpanel.resources.store', { resource: props.resource }), {
        forceFormData: hasFiles, // Use FormData if files are present
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>
