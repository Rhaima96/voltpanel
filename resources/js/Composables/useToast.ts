import { router } from '@inertiajs/vue3';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

export function useToast() {
  const showToast = (type: ToastType, message: string): void => {
    router.reload({
      only: [],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        // Flash message will be handled by the Toast component
      }
    });

    const flashData: Record<string, string> = {};
    flashData[type] = message;

    window.dispatchEvent(new CustomEvent('toast', {
      detail: { type, message }
    }));
  };

  return {
    success: (message: string) => showToast('success', message),
    error: (message: string) => showToast('error', message),
    warning: (message: string) => showToast('warning', message),
    info: (message: string) => showToast('info', message),
  };
}
