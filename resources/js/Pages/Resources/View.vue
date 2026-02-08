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

        <!-- View Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="space-y-6">
                <template v-for="(component, index) in form.components" :key="index">
                    <!-- Section Component -->
                    <div v-if="component.type === 'section'" class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div v-if="component.heading" class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ component.heading }}
                            </h3>
                            <p v-if="component.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ component.description }}
                            </p>
                        </div>
                        <div
                            class="gap-6"
                            :class="component.columns > 1 ? `grid grid-cols-${component.columns}` : 'space-y-6'"
                        >
                            <template
                                v-for="field in component.components"
                                :key="field.name"
                            >
                                <!-- Comments Field (Read-only) -->
                                <div v-if="field.type === 'comments'" class="col-span-full">
                                    <div class="flex items-center gap-2 mb-4">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h4>
                                        <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300">
                                            {{ (record.comments || []).length }}
                                        </span>
                                    </div>
                                    <div v-if="sortedComments.length > 0" class="space-y-4">
                                        <div
                                            v-for="comment in sortedComments"
                                            :key="comment.id"
                                            class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4"
                                        >
                                            <div class="flex items-start gap-3">
                                                <img
                                                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user?.name || 'User')}&background=random`"
                                                    :alt="comment.user?.name"
                                                    class="w-10 h-10 rounded-full ring-2 ring-gray-200 dark:ring-gray-600 flex-shrink-0"
                                                />
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-semibold text-gray-900 dark:text-white">
                                                            {{ comment.user?.name || 'Unknown User' }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ formatDate(comment.created_at) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words" v-html="highlightMentions(comment.content)"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-8 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet</p>
                                    </div>
                                </div>
                                <div v-else>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        {{ field.label }}
                                    </dt>
                                    <dd class="text-base text-gray-900 dark:text-white">
                                        <!-- File Upload Field -->
                                        <template v-if="field.type === 'file-upload'">
                                        <img
                                            v-if="field.image && record[field.name]"
                                            :src="getFileUrl(record[field.name])"
                                            :alt="field.label"
                                            class="max-h-48 rounded-lg border border-gray-200 dark:border-gray-700"
                                        />
                                        <a
                                            v-else-if="record[field.name]"
                                            :href="getFileUrl(record[field.name])"
                                            target="_blank"
                                            class="text-primary-600 dark:text-primary-400 hover:underline"
                                        >
                                            {{ getFileName(record[field.name]) }}
                                        </a>
                                        <span v-else>—</span>
                                    </template>
                                    <!-- Rich Editor Field -->
                                    <template v-else-if="field.type === 'rich-editor'">
                                        <div
                                            v-if="record[field.name]"
                                            v-html="record[field.name]"
                                            class="prose prose-sm dark:prose-invert max-w-none"
                                        ></div>
                                        <span v-else>—</span>
                                    </template>
                                        <!-- Other Fields -->
                                        <template v-else>
                                            {{ getFieldValue(field, record[field.name]) }}
                                        </template>
                                    </dd>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- Comments Field Component (Read-only) -->
                    <div v-else-if="component.type === 'comments'" class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Comments</h4>
                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300">
                                {{ (record.comments || []).length }}
                            </span>
                        </div>
                        <div v-if="sortedComments.length > 0" class="space-y-4">
                            <div
                                v-for="comment in sortedComments"
                                :key="comment.id"
                                class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4"
                            >
                                <div class="flex items-start gap-3">
                                    <img
                                        :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user?.name || 'User')}&background=random`"
                                        :alt="comment.user?.name"
                                        class="w-10 h-10 rounded-full ring-2 ring-gray-200 dark:ring-gray-600 flex-shrink-0"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                {{ comment.user?.name || 'Unknown User' }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatDate(comment.created_at) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words" v-html="highlightMentions(comment.content)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet</p>
                        </div>
                    </div>
                    <!-- Regular Field Component -->
                    <div
                        v-else
                        class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0"
                    >
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            {{ component.label }}
                        </dt>
                        <dd class="text-base text-gray-900 dark:text-white">
                            <!-- File Upload Field -->
                            <template v-if="component.type === 'file-upload'">
                                <img
                                    v-if="component.image && record[component.name]"
                                    :src="getFileUrl(record[component.name])"
                                    :alt="component.label"
                                    class="max-h-48 rounded-lg border border-gray-200 dark:border-gray-700"
                                />
                                <a
                                    v-else-if="record[component.name]"
                                    :href="getFileUrl(record[component.name])"
                                    target="_blank"
                                    class="text-primary-600 dark:text-primary-400 hover:underline"
                                >
                                    {{ getFileName(record[component.name]) }}
                                </a>
                                <span v-else>—</span>
                            </template>
                            <!-- Rich Editor Field -->
                            <template v-else-if="component.type === 'rich-editor'">
                                <div
                                    v-if="record[component.name]"
                                    v-html="record[component.name]"
                                    class="prose prose-sm dark:prose-invert max-w-none"
                                ></div>
                                <span v-else>—</span>
                            </template>
                            <!-- Other Fields -->
                            <template v-else>
                                {{ getFieldValue(component, record[component.name]) }}
                            </template>
                        </dd>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <button
                    v-if="canDelete"
                    type="button"
                    @click="showDeleteModal = true"
                    class="px-4 py-2 bg-destructive text-destructive-foreground rounded-md hover:opacity-90 transition-all"
                >
                    Delete
                </button>

                <div class="flex items-center space-x-3 ml-auto">
                    <Link
                        :href="route('voltpanel.resources.index', { resource })"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Back
                    </Link>
                    <Link
                        v-if="canEdit"
                        :href="route('voltpanel.resources.edit', { resource, record: record.id })"
                        class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-all focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        Edit
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            title="Delete Record"
            message="Are you sure you want to delete this record? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            type="danger"
            @confirm="confirmDelete"
            @close="showDeleteModal = false"
        />
    </PanelLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PanelLayout from '../../../layouts/VoltPanel/PanelLayout.vue';
import ConfirmationModal from '../../../components/VoltPanel/ConfirmationModal.vue';
import { useRouting } from '../../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const props = defineProps({
    resource: String,
    title: String,
    form: Object,
    record: Object,
    canEdit: Boolean,
    canDelete: Boolean,
});

const showDeleteModal = ref(false);

// Sort comments with newest first
const sortedComments = computed(() => {
    if (!props.record.comments || !Array.isArray(props.record.comments)) {
        return [];
    }
    return [...props.record.comments].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

// Get file URL for display
const getFileUrl = (filePath) => {
    if (!filePath) return '';

    // If it's already a full URL, return it
    if (filePath.startsWith('http://') || filePath.startsWith('https://')) {
        return filePath;
    }

    // If it's a storage path, construct the URL
    if (filePath.startsWith('storage/') || filePath.startsWith('/storage/')) {
        return filePath.startsWith('/') ? filePath : '/' + filePath;
    }

    // Default: assume it's in storage
    return '/storage/' + filePath;
};

// Get file name from path
const getFileName = (filePath) => {
    if (!filePath) return '—';

    // Extract filename from path
    const parts = filePath.split(/[/\\]/);
    return parts[parts.length - 1];
};

// Get field value with proper formatting
const getFieldValue = (field, value) => {
    if (value === null || value === undefined) {
        return '—';
    }

    // Handle select fields with options
    if (field.type === 'select' && field.options) {
        // Handle multiple select
        if (field.multiple && Array.isArray(value)) {
            const labels = value.map(v => field.options[v] || v).filter(Boolean);
            return labels.length > 0 ? labels.join(', ') : '—';
        }

        // Handle single select
        return field.options[value] || value || '—';
    }

    // Handle toggle/checkbox
    if (field.type === 'toggle' || field.type === 'checkbox') {
        return value ? 'Yes' : 'No';
    }

    // Handle arrays
    if (Array.isArray(value)) {
        return value.length > 0 ? value.join(', ') : '—';
    }

    return value || '—';
};

const confirmDelete = () => {
    router.delete(route('voltpanel.resources.destroy', { resource: props.resource, record: props.record.id }), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
        onError: () => {
            showDeleteModal.value = false;
        },
    });
};

// Format date for comments
const formatDate = (date) => {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (days > 7) {
        return d.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: d.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
        });
    } else if (days > 0) {
        return `${days} day${days > 1 ? 's' : ''} ago`;
    } else if (hours > 0) {
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (minutes > 0) {
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else {
        return 'Just now';
    }
};

// Highlight @mentions in comments
const highlightMentions = (content) => {
    if (!content) return '';
    const escaped = content
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    return escaped.replace(/@(\w+)/g, '<span class="text-primary-600 dark:text-primary-400 font-semibold">@$1</span>');
};
</script>

<style>
/* Rich editor content styling in view */
.prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1rem 0;
}

/* File attachment styling in view */
.prose .file-attachment-link {
    display: block;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: opacity 0.2s;
}

.prose .file-attachment-link:hover {
    opacity: 0.8;
}

.prose p:has(.file-attachment-link) {
    background-color: #f3f4f6;
    padding: 12px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    margin: 8px 0;
}

/* Dark mode support */
.dark .prose p:has(.file-attachment-link) {
    background-color: #374151 !important;
    border-color: #60a5fa !important;
}

.dark .prose .file-attachment-link span[style*="color: #6b7280"] {
    color: #9ca3af !important;
}

/* Ensure prose styling doesn't interfere with file attachments */
.prose .file-attachment-link strong {
    color: inherit;
}
</style>
