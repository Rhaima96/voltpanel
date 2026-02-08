<template>
    <div class="space-y-2">
        <!-- Existing File Preview -->
        <div v-if="existingFileUrl" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
            <div class="flex items-start justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Current file:</span>
                <button
                    v-if="!component.required"
                    type="button"
                    @click="clearExistingFile"
                    class="text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                >
                    Remove
                </button>
            </div>
            <div v-if="component.image" class="flex justify-center">
                <img
                    :src="existingFileUrl"
                    :alt="component.label"
                    class="max-h-48 rounded-lg border border-gray-200 dark:border-gray-700"
                />
            </div>
            <div v-else class="flex items-center space-x-3">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ existingFileName }}</p>
                    <a
                        :href="existingFileUrl"
                        target="_blank"
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline"
                    >
                        Download
                    </a>
                </div>
            </div>
        </div>

        <div
            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 hover:border-primary-500 dark:hover:border-primary-400 transition-colors"
            @dragover.prevent="dragover = true"
            @dragleave.prevent="dragover = false"
            @drop.prevent="handleDrop"
            :class="{ 'border-primary-500 dark:border-primary-400 bg-primary-50 dark:bg-primary-900/10': dragover }"
        >
            <input
                :id="component.name"
                type="file"
                :accept="component.acceptedFileTypes?.join(',')"
                :multiple="component.multiple"
                :required="component.required && !existingFileUrl"
                @change="handleFileChange"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            />
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-primary-600 dark:text-primary-400">
                        {{ existingFileUrl ? 'Click to replace' : 'Click to upload' }}
                    </span>
                    or drag and drop
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                    <template v-if="component.image">
                        Images only
                    </template>
                    <template v-if="component.maxSize">
                        (Max {{ formatFileSize(component.maxSize * 1024) }})
                    </template>
                </p>
            </div>
        </div>

        <!-- New File Preview -->
        <div v-if="filePreview" class="relative">
            <img
                v-if="component.image && filePreview"
                :src="filePreview"
                alt="Preview"
                class="max-h-48 rounded-lg border border-gray-200 dark:border-gray-700"
            />
            <div v-else-if="fileName" class="flex items-center space-x-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ fileName }}</span>
            </div>
            <button
                type="button"
                @click="clearFile"
                class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [String, File],
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const dragover = ref(false);
const filePreview = ref(null);
const fileName = ref(null);
const existingFileUrl = ref(null);
const existingFileName = ref(null);

// Helper to get file URL from path
const getFileUrl = (path) => {
    if (!path) return null;
    // If already a full URL, return as is
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
    }
    // Otherwise, prepend /storage/ for Laravel public disk
    return `/storage/${path}`;
};

// Helper to get filename from path
const getFileName = (path) => {
    if (!path) return null;
    return path.split('/').pop();
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        processFile(file);
    }
};

const handleDrop = (event) => {
    dragover.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        processFile(file);
    }
};

const processFile = (file) => {
    // Validate file size
    if (props.component.maxSize && file.size > props.component.maxSize * 1024) {
        alert(`File size exceeds maximum of ${formatFileSize(props.component.maxSize * 1024)}`);
        return;
    }

    // Validate file type
    if (props.component.acceptedFileTypes && props.component.acceptedFileTypes.length > 0) {
        const fileType = file.type;
        const isValid = props.component.acceptedFileTypes.some(type => {
            if (type.endsWith('/*')) {
                return fileType.startsWith(type.replace('/*', ''));
            }
            return fileType === type;
        });

        if (!isValid) {
            alert('File type not accepted');
            return;
        }
    }

    // Clear existing file display when new file is selected
    existingFileUrl.value = null;
    existingFileName.value = null;

    fileName.value = file.name;
    emit('update:modelValue', file);

    // Create preview for images
    if (props.component.image && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            filePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        filePreview.value = null;
    }
};

const clearFile = () => {
    filePreview.value = null;
    fileName.value = null;
    emit('update:modelValue', null);
};

const clearExistingFile = () => {
    existingFileUrl.value = null;
    existingFileName.value = null;
    emit('update:modelValue', null);
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Watch for external changes to modelValue (e.g., when editing)
watch(() => props.modelValue, (newValue) => {
    if (newValue && typeof newValue === 'string') {
        // If modelValue is a file path string (existing file)
        existingFileUrl.value = getFileUrl(newValue);
        existingFileName.value = getFileName(newValue);
        // Clear new file preview when showing existing file
        filePreview.value = null;
        fileName.value = null;
    } else if (newValue instanceof File) {
        // New file selected, clear existing file display
        existingFileUrl.value = null;
        existingFileName.value = null;
    } else if (!newValue) {
        // No file
        existingFileUrl.value = null;
        existingFileName.value = null;
        filePreview.value = null;
        fileName.value = null;
    }
}, { immediate: true });
</script>
