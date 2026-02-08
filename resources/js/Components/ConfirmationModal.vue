<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 dark:bg-black/70" @click="closeModal"></div>

                <!-- Modal Container -->
                <div class="flex min-h-full items-center justify-center p-4" @click.self="closeModal">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="show"
                            class="relative w-full max-w-md transform rounded-lg bg-white dark:bg-gray-800 shadow-xl transition-all"
                        >
                            <!-- Modal Content -->
                            <div class="p-6">
                                <!-- Icon -->
                                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full"
                                     :class="iconBackgroundClass">
                                    <svg class="w-6 h-6" :class="iconClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>

                                <!-- Title -->
                                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-2">
                                    {{ title }}
                                </h3>

                                <!-- Message -->
                                <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-6">
                                    {{ message }}
                                </p>

                                <!-- Actions -->
                                <div class="flex items-center gap-3">
                                    <button
                                        @click="closeModal"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                                    >
                                        {{ cancelText }}
                                    </button>
                                    <button
                                        @click="confirmAction"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                                        :class="confirmButtonClass"
                                    >
                                        {{ confirmText }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirm Action',
    },
    message: {
        type: String,
        default: 'Are you sure you want to perform this action?',
    },
    confirmText: {
        type: String,
        default: 'Confirm',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
    type: {
        type: String,
        default: 'danger', // danger, warning, info
        validator: (value) => ['danger', 'warning', 'info'].includes(value),
    },
});

const emit = defineEmits(['confirm', 'cancel', 'close']);

const iconBackgroundClass = computed(() => {
    const classes = {
        danger: 'bg-red-100 dark:bg-red-900/20',
        warning: 'bg-yellow-100 dark:bg-yellow-900/20',
        info: 'bg-blue-100 dark:bg-blue-900/20',
    };
    return classes[props.type];
});

const iconClass = computed(() => {
    const classes = {
        danger: 'text-red-600 dark:text-red-400',
        warning: 'text-yellow-600 dark:text-yellow-400',
        info: 'text-blue-600 dark:text-blue-400',
    };
    return classes[props.type];
});

const confirmButtonClass = computed(() => {
    const classes = {
        danger: 'bg-destructive text-destructive-foreground hover:opacity-90 focus:ring-ring',
        warning: 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        info: 'bg-primary text-primary-foreground hover:opacity-90 focus:ring-ring',
    };
    return classes[props.type];
});

const confirmAction = () => {
    emit('confirm');
    emit('close');
};

const closeModal = () => {
    emit('cancel');
    emit('close');
};
</script>
