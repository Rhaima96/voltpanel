<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <TransitionGroup name="notification">
            <div
                v-for="notification in notifications"
                :key="notification.id"
                class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="h-6 w-6 rounded-full flex items-center justify-center"
                                :class="getStatusClass(notification.status)"
                            >
                                <svg v-if="notification.status === 'success'" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else-if="notification.status === 'danger'" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else-if="notification.status === 'warning'" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ notification.title }}
                            </p>
                            <p v-if="notification.body" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ notification.body }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button
                                @click="removeNotification(notification.id)"
                                class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const notifications = ref([]);
let notificationId = 0;

const addNotification = (notification) => {
    const id = ++notificationId;
    const newNotification = { ...notification, id };
    notifications.value.push(newNotification);

    if (notification.duration > 0) {
        setTimeout(() => {
            removeNotification(id);
        }, notification.duration);
    }
};

const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
        notifications.value.splice(index, 1);
    }
};

const getStatusClass = (status) => {
    const classes = {
        success: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        danger: 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300',
        warning: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
        info: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
    };
    return classes[status] || classes.info;
};

// Expose method to add notifications globally
onMounted(() => {
    window.$notify = addNotification;
});
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}

.notification-enter-from {
    opacity: 0;
    transform: translateX(2rem);
}

.notification-leave-to {
    opacity: 0;
    transform: translateY(-1rem);
}
</style>
