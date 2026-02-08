<template>
    <div class="inline-flex items-center gap-2">
        <!-- Edit Action -->
        <Link
            v-if="action.type === 'edit'"
            :href="route('voltpanel.resources.edit', { resource, record: record.id })"
            :class="getActionClasses(action.color)"
        >
            {{ action.label }}
        </Link>

        <!-- View Action -->
        <Link
            v-else-if="action.type === 'view'"
            :href="route('voltpanel.resources.view', { resource, record: record.id })"
            :class="getActionClasses(action.color)"
        >
            {{ action.label }}
        </Link>

        <!-- Delete Action -->
        <button
            v-else-if="action.type === 'delete'"
            @click="showDeleteModal = true"
            :class="getActionClasses(action.color)"
        >
            {{ action.label }}
        </button>

        <!-- Custom Action -->
        <button
            v-else
            :class="getActionClasses(action.color)"
        >
            {{ action.label }}
        </button>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            v-if="action.type === 'delete'"
            :show="showDeleteModal"
            :title="action.confirmationTitle || 'Delete Record'"
            :message="action.confirmationMessage || 'Are you sure you want to delete this record? This action cannot be undone.'"
            confirm-text="Delete"
            cancel-text="Cancel"
            type="danger"
            @confirm="handleDelete"
            @close="showDeleteModal = false"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmationModal from './ConfirmationModal.vue';
import { useRouting } from '../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const props = defineProps({
    action: {
        type: Object,
        required: true,
    },
    record: {
        type: Object,
        required: true,
    },
    resource: {
        type: String,
        required: true,
    },
});

const showDeleteModal = ref(false);

const getActionClasses = (color) => {
    const baseClasses = 'font-medium hover:underline';

    const colorClasses = {
        primary: 'text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300',
        red: 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300',
        gray: 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300',
        green: 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300',
        blue: 'text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300',
    };

    return `${baseClasses} ${colorClasses[color] || colorClasses.gray}`;
};

const handleDelete = () => {
    router.delete(route('voltpanel.resources.destroy', { resource: props.resource, record: props.record.id }), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
        },
        onError: () => {
            // Error will be handled by Inertia's error handling
            showDeleteModal.value = false;
        },
    });
};
</script>
