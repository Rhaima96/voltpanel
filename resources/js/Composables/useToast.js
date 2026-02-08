import { router } from '@inertiajs/vue3'

export function useToast() {
    const showToast = (type, message) => {
        // Use Inertia's router to reload with flash message
        router.reload({
            only: [],
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // Flash message will be handled by the Toast component
                // which watches page.props.flash
            }
        })

        // Set flash message in session
        const flashData = {}
        flashData[type] = message

        // Trigger a custom event for the Toast component
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type, message }
        }))
    }

    return {
        success: (message) => showToast('success', message),
        error: (message) => showToast('error', message),
        warning: (message) => showToast('warning', message),
        info: (message) => showToast('info', message),
    }
}
