import { ref, onMounted, onUnmounted } from 'vue';

const isDark = ref(false);
let mediaQueryListener = null;
let mediaQuery = null;

/**
 * Detect Tailwind CSS version based on CSS features
 * @returns {3 | 4}
 */
export const detectTailwindVersion = () => {
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
 * @returns {'class' | 'selector'}
 */
export const getDarkModeStrategy = () => {
  // Both v3 and v4 support class-based dark mode
  // This is the most compatible approach
  return 'class';
};

/**
 * Composable for managing dark mode with Tailwind CSS v3/v4 support
 * @param {Object} options
 * @param {string} [options.storageKey='voltpanel-dark-mode']
 * @param {boolean} [options.defaultDark=false]
 */
export function useDarkMode(options = {}) {
  const {
    storageKey = 'voltpanel-dark-mode',
    defaultDark = false,
  } = options;

  const tailwindVersion = detectTailwindVersion();

  const toggle = () => {
    isDark.value = !isDark.value;
    updateDOM();
    savePreference();
  };

  const setDark = (value) => {
    isDark.value = value;
    updateDOM();
    savePreference();
  };

  const updateDOM = () => {
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

  const savePreference = () => {
    try {
      localStorage.setItem(storageKey, isDark.value ? '1' : '0');
    } catch {
      // localStorage might be unavailable
    }
  };

  const loadPreference = () => {
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

  const setupSystemPreferenceListener = () => {
    if (typeof window === 'undefined') return;

    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQueryListener = (e) => {
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

  const cleanup = () => {
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
