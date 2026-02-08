import { onMounted, onUnmounted } from 'vue';

export type ShortcutCallback = () => void;
export type ShortcutMap = Record<string, ShortcutCallback>;

export interface UseKeyboardShortcutsReturn {
  handleKeyDown: (event: KeyboardEvent) => void;
  getKeyCombo: (event: KeyboardEvent) => string;
}

export function useKeyboardShortcuts(shortcuts: ShortcutMap): UseKeyboardShortcutsReturn {
  const handleKeyDown = (event: KeyboardEvent): void => {
    const key = getKeyCombo(event);

    if (shortcuts[key]) {
      event.preventDefault();
      shortcuts[key]();
    }
  };

  const getKeyCombo = (event: KeyboardEvent): string => {
    const keys: string[] = [];

    if (event.ctrlKey || event.metaKey) keys.push('ctrl');
    if (event.shiftKey) keys.push('shift');
    if (event.altKey) keys.push('alt');

    keys.push(event.key.toLowerCase());

    return keys.join('+');
  };

  onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
  });

  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);
  });

  return {
    handleKeyDown,
    getKeyCombo
  };
}
