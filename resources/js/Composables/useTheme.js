import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { detectTailwindVersion } from './useDarkMode';

/**
 * Detect the color format used in CSS variables
 * @returns {'hex' | 'hsl-raw' | 'hsl-wrapped' | 'oklch'}
 */
const detectColorFormat = () => {
  if (typeof window === 'undefined') return 'hex';

  const root = getComputedStyle(document.documentElement);

  // Check for shadcn-style variables
  const primaryValue = root.getPropertyValue('--primary').trim();

  if (primaryValue) {
    if (primaryValue.startsWith('oklch(')) return 'oklch';
    if (primaryValue.startsWith('hsl(')) return 'hsl-wrapped';
    if (/^\d+(\.\d+)?\s+\d+(\.\d+)?%\s+\d+(\.\d+)?%$/.test(primaryValue)) return 'hsl-raw';
  }

  // Check VoltPanel custom variables
  const vpPrimary = root.getPropertyValue('--vp-primary').trim();
  if (vpPrimary) {
    if (vpPrimary.startsWith('#')) return 'hex';
    if (vpPrimary.startsWith('oklch(')) return 'oklch';
    if (vpPrimary.startsWith('hsl(')) return 'hsl-wrapped';
  }

  return 'hex';
};

/**
 * Convert hex color to HSL values
 * @param {string} hex
 * @returns {{ h: number, s: number, l: number } | null}
 */
const hexToHSL = (hex) => {
  if (!hex || !hex.startsWith('#')) return null;

  hex = hex.replace('#', '');
  if (hex.length === 3) {
    hex = hex.split('').map(c => c + c).join('');
  }

  const r = parseInt(hex.substring(0, 2), 16) / 255;
  const g = parseInt(hex.substring(2, 4), 16) / 255;
  const b = parseInt(hex.substring(4, 6), 16) / 255;

  const max = Math.max(r, g, b);
  const min = Math.min(r, g, b);
  let h = 0;
  let s = 0;
  const l = (max + min) / 2;

  if (max !== min) {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

    switch (max) {
      case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
      case g: h = ((b - r) / d + 2) / 6; break;
      case b: h = ((r - g) / d + 4) / 6; break;
    }
  }

  return {
    h: Math.round(h * 360),
    s: Math.round(s * 100),
    l: Math.round(l * 100),
  };
};

/**
 * Format HSL values based on the target format
 * @param {{ h: number, s: number, l: number }} hsl
 * @param {'hex' | 'hsl-raw' | 'hsl-wrapped' | 'oklch'} format
 * @returns {string}
 */
const formatHSL = (hsl, format) => {
  switch (format) {
    case 'hsl-raw':
      return `${hsl.h} ${hsl.s}% ${hsl.l}%`;
    case 'hsl-wrapped':
      return `hsl(${hsl.h} ${hsl.s}% ${hsl.l}%)`;
    case 'oklch':
      // Approximate conversion from HSL to OKLCH
      const l = hsl.l / 100;
      const c = (hsl.s / 100) * Math.min(l, 1 - l) * 0.4;
      return `oklch(${(l * 100).toFixed(1)}% ${c.toFixed(3)} ${hsl.h})`;
    default:
      return `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`;
  }
};

/**
 * Convert hex to the appropriate color format
 * @param {string} hex
 * @param {'hex' | 'hsl-raw' | 'hsl-wrapped' | 'oklch'} format
 * @returns {string | null}
 */
const convertColor = (hex, format) => {
  if (format === 'hex') return hex;

  const hsl = hexToHSL(hex);
  if (!hsl) return null;

  return formatHSL(hsl, format);
};

/**
 * Calculate contrasting foreground color
 * @param {string} hex
 * @param {'hex' | 'hsl-raw' | 'hsl-wrapped' | 'oklch'} format
 * @returns {string}
 */
const getContrastingForeground = (hex, format) => {
  const darkColor = { h: 222, s: 47, l: 11 };
  const lightColor = { h: 210, s: 40, l: 98 };

  if (!hex || !hex.startsWith('#')) {
    return formatHSL(lightColor, format);
  }

  hex = hex.replace('#', '');
  const r = parseInt(hex.substring(0, 2), 16);
  const g = parseInt(hex.substring(2, 4), 16);
  const b = parseInt(hex.substring(4, 6), 16);

  // Calculate relative luminance
  const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

  // Return dark text for light backgrounds, light text for dark backgrounds
  return formatHSL(luminance > 0.5 ? darkColor : lightColor, format);
};

/**
 * Composable for managing theme with Tailwind CSS v3/v4 support
 */
export function useTheme() {
  const page = usePage();
  const theme = computed(() => page.props.voltpanel?.theme || null);
  const tailwindVersion = computed(() => detectTailwindVersion());
  const colorFormat = computed(() => detectColorFormat());

  /**
   * Get CSS variable names based on Tailwind version
   * @param {string} colorKey
   * @param {3 | 4} version
   * @returns {{ main: string[], foreground: string[] }}
   */
  const getColorVars = (colorKey, version) => {
    const baseVars = {
      primary: {
        main: ['--vp-primary', '--primary'],
        foreground: ['--primary-foreground'],
      },
      success: {
        main: ['--success'],
        foreground: ['--success-foreground'],
      },
      danger: {
        main: ['--destructive', '--danger'],
        foreground: ['--destructive-foreground', '--danger-foreground'],
      },
      warning: {
        main: ['--warning'],
        foreground: ['--warning-foreground'],
      },
      info: {
        main: ['--info'],
        foreground: ['--info-foreground'],
      },
    };

    const vars = baseVars[colorKey] || { main: [], foreground: [] };

    // Add v4-specific variables
    if (version === 4) {
      vars.main = [...vars.main, `--color-${colorKey}`];
      vars.foreground = [...vars.foreground, `--color-${colorKey}-foreground`];
    }

    return vars;
  };

  /**
   * Apply a color to CSS variables
   * @param {string} colorKey
   * @param {string} hexValue
   * @param {'hex' | 'hsl-raw' | 'hsl-wrapped' | 'oklch'} format
   * @param {3 | 4} version
   * @param {Object} colors
   */
  const applyColor = (colorKey, hexValue, format, version, colors) => {
    const root = document.documentElement;
    const vars = getColorVars(colorKey.replace('-foreground', ''), version);

    // Convert and apply main color
    const convertedColor = format === 'hex' ? hexValue : convertColor(hexValue, format);
    if (!convertedColor) return;

    vars.main.forEach(cssVar => {
      root.style.setProperty(cssVar, convertedColor);
    });

    // Handle foreground color
    const foregroundKey = `${colorKey}-foreground`;
    const foregroundHex = colors[foregroundKey];

    let foregroundColor;
    if (foregroundHex) {
      foregroundColor = format === 'hex' ? foregroundHex : (convertColor(foregroundHex, format) || getContrastingForeground(hexValue, format));
    } else {
      foregroundColor = getContrastingForeground(hexValue, format);
    }

    vars.foreground.forEach(cssVar => {
      root.style.setProperty(cssVar, foregroundColor);
    });
  };

  /**
   * Apply theme to the document
   */
  const applyTheme = () => {
    const themeValue = theme.value;
    if (!themeValue) return;

    const colors = themeValue.colors || {};
    const version = tailwindVersion.value;
    const format = colorFormat.value;
    const root = document.documentElement;

    // Apply each color
    const colorKeys = ['primary', 'success', 'danger', 'warning', 'info'];

    colorKeys.forEach(key => {
      const hexValue = colors[key];
      if (hexValue) {
        applyColor(key, hexValue, format, version, colors);
      }
    });

    // Apply ring color for focus states (uses primary)
    if (colors.primary) {
      const ringColor = format === 'hex' ? colors.primary : convertColor(colors.primary, format);
      if (ringColor) {
        root.style.setProperty('--ring', ringColor);
        if (version === 4) {
          root.style.setProperty('--color-ring', ringColor);
        }
      }
    }

    // Apply custom font
    if (themeValue.font) {
      root.style.setProperty('font-family', themeValue.font);
    }

    // Apply custom CSS
    if (themeValue.customCss && Object.keys(themeValue.customCss).length > 0) {
      let customStyles = '';
      for (const [selector, styles] of Object.entries(themeValue.customCss)) {
        customStyles += `${selector} { ${styles} }\n`;
      }

      // Remove existing custom style tag
      const existingStyle = document.getElementById('voltpanel-custom-theme');
      if (existingStyle) {
        existingStyle.remove();
      }

      // Add new custom style tag
      const styleTag = document.createElement('style');
      styleTag.id = 'voltpanel-custom-theme';
      styleTag.textContent = customStyles;
      document.head.appendChild(styleTag);
    }
  };

  // Watch for theme changes
  watch(() => theme.value, applyTheme, { immediate: true, deep: true });

  return {
    theme,
    applyTheme,
    tailwindVersion,
    colorFormat,
  };
}

export default useTheme;
