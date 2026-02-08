<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm">
            <TransitionGroup
                enter-active-class="transition ease-out duration-300"
                enter-from-class="translate-x-full opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-full opacity-0"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="flex items-start gap-3 p-4 rounded-lg shadow-lg backdrop-blur-sm"
                    :class="getToastClasses(toast.type)"
                >
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else-if="toast.type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else-if="toast.type === 'warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <!-- Message -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">
                            {{ toast.message }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="removeToast(toast.id)"
                        class="flex-shrink-0 ml-2 opacity-70 hover:opacity-100 transition-opacity"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const toasts = ref([])

const getToastClasses = (type) => ({
    success: 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800',
    error: 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800',
    warning: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800',
    info: 'bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800',
}[type] ?? 'bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200')

let toastId = 0

const addToast = (type, message) => {
    if (!message) return

    const id = ++toastId

    toasts.value.push({ id, type, message })

    setTimeout(() => removeToast(id), 5000)
}

const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id)
}

watch(
    () => page.props,
    async (props) => {
        await nextTick() // 🔥 CRITICAL

        const flash = props.flash
        if (!flash) return

        if (flash.success) {
            addToast('success', flash.success)
        }

        if (flash.error) {
            addToast('error', flash.error)
        }

        if (flash.warning) {
            addToast('warning', flash.warning)
        }

        if (flash.info) {
            addToast('info', flash.info)
        }
    },
    { immediate: true }
)


/* ✅ Custom JS events support */
const handleToastEvent = (event) => {
    const { type = 'info', message } = event.detail || {}
    addToast(type, message)
}

onMounted(() => {
    window.addEventListener('toast', handleToastEvent)
})

onUnmounted(() => {
    window.removeEventListener('toast', handleToastEvent)
})
</script>

