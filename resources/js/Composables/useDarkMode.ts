import { ref, onMounted, onUnmounted, type Ref } from 'vue';

export type DarkModeStrategy = 'class' | 'selector';
export type TailwindVersion = 3 | 4;

interface DarkModeOptions {
  storageKey?: string;
  defaultDark?: boolean;
}

const isDark: Ref<boolean> = ref(false);
let mediaQueryListener: ((e: MediaQueryListEvent) => void) | null = null;
let mediaQuery: MediaQueryList | null = null;

/**
 * Detect Tailwind CSS version based on CSS features
 */
export const detectTailwindVersion = (): TailwindVersion => {
  if (typeof window === 'undefined') return 3;

  const root = getComputedStyle(document.documentElement);

  // Tailwind v4 uses @theme which creates --color-* variables
  const hasV4ColorVars = root.getPropertyValue('--color-primary').trim() !== '' ||
                         root.getPropertyValue('--color-background').trim() !== '';

  if (hasV4ColorVars) return 4;

  // Check stylesheets for v4 indicators
  try {
    for (const sheet of document.styleSheets) {
      try {
        if (sheet.cssRules) {
          for (const rule of sheet.cssRules) {
            if (rule.cssText?.includes('--color-primary:')) {
              return 4;
            }
          }
        }
      } catch {
        // Skip cross-origin stylesheets
      }
    }
  } catch {
    // Fallback
  }

  return 3;
};

/**
 * Get the dark mode strategy based on Tailwind version
 * - v3: Uses 'class' strategy (darkMode: 'class' in config)
 * - v4: Uses 'class' by default, can also use 'selector'
 */
export const getDarkModeStrategy = (): DarkModeStrategy => {
  // Both v3 and v4 support class-based dark mode
  // This is the most compatible approach
  return 'class';
};

/**
 * Composable for managing dark mode with Tailwind CSS v3/v4 support
 */
export function useDarkMode(options: DarkModeOptions = {}) {
  const {
    storageKey = 'voltpanel-dark-mode',
    defaultDark = false,
  } = options;

  const tailwindVersion = detectTailwindVersion();

  const toggle = (): void => {
    isDark.value = !isDark.value;
    updateDOM();
    savePreference();
  };

  const setDark = (value: boolean): void => {
    isDark.value = value;
    updateDOM();
    savePreference();
  };

  const updateDOM = (): void => {
    const strategy = getDarkModeStrategy();

    if (strategy === 'class') {
      // Class-based dark mode (works for both v3 and v4)
      if (isDark.value) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    }

    // Dispatch event for other components to react
    window.dispatchEvent(new CustomEvent('darkmode-change', {
      detail: { isDark: isDark.value, tailwindVersion }
    }));
  };

  const savePreference = (): void => {
    try {
      localStorage.setItem(storageKey, isDark.value ? '1' : '0');
    } catch {
      // localStorage might be unavailable
    }
  };

  const loadPreference = (): void => {
    try {
      const saved = localStorage.getItem(storageKey);
      if (saved !== null) {
        isDark.value = saved === '1';
      } else if (typeof window !== 'undefined') {
        // Respect system preference if no saved preference
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
      } else {
        isDark.value = defaultDark;
      }
    } catch {
      isDark.value = defaultDark;
    }
    updateDOM();
  };

  const setupSystemPreferenceListener = (): void => {
    if (typeof window === 'undefined') return;

    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQueryListener = (e: MediaQueryListEvent): void => {
      // Only follow system preference if user hasn't set a preference
      try {
        if (localStorage.getItem(storageKey) === null) {
          isDark.value = e.matches;
          updateDOM();
        }
      } catch {
        isDark.value = e.matches;
        updateDOM();
      }
    };
    mediaQuery.addEventListener('change', mediaQueryListener);
  };

  const cleanup = (): void => {
    if (mediaQuery && mediaQueryListener) {
      mediaQuery.removeEventListener('change', mediaQueryListener);
      mediaQueryListener = null;
      mediaQuery = null;
    }
  };

  onMounted(() => {
    loadPreference();
    setupSystemPreferenceListener();
  });

  onUnmounted(() => {
    cleanup();
  });

  return {
    isDark,
    toggle,
    setDark,
    tailwindVersion,
  };
}

export default useDarkMode;
