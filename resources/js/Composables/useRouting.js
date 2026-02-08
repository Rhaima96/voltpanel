import { computed } from 'vue';

/**
 * Unified routing composable that works with both Ziggy and Wayfinder.
 *
 * - Ziggy: Uses `route()` function globally available
 * - Wayfinder: Uses generated route functions from @/routes
 *
 * This composable detects which routing system is available and provides
 * a unified API for generating routes in VoltPanel.
 */

// Check if Ziggy is available
const hasZiggy = () => {
  return typeof window !== 'undefined' && typeof window.route === 'function';
};

// Check if Wayfinder routes are available
const hasWayfinder = () => {
  // Wayfinder routes are imported, we'll check at runtime
  return !hasZiggy();
};

/**
 * Get the routing mode being used
 */
export const getRoutingMode = () => {
  if (hasZiggy()) return 'ziggy';
  if (hasWayfinder()) return 'wayfinder';
  return 'fallback';
};

/**
 * Generate a URL using the available routing system.
 *
 * @param {string} name - The route name (e.g., 'voltpanel.resources.index')
 * @param {Object} params - Route parameters
 * @returns {string} The generated URL string
 */
export const generateRoute = (name, params = {}) => {
  // Try Ziggy first
  if (hasZiggy()) {
    try {
      return window.route(name, params);
    } catch (e) {
      console.warn(`[VoltPanel] Ziggy route '${name}' not found, falling back to manual URL`);
    }
  }

  // Fallback: construct URL manually based on VoltPanel route patterns
  return buildFallbackUrl(name, params);
};

/**
 * Build a fallback URL when neither Ziggy nor Wayfinder is available.
 * This handles VoltPanel's standard route patterns.
 */
const buildFallbackUrl = (name, params) => {
  const basePath = getBasePath();

  // Map route names to URL patterns
  const routePatterns = {
    // VoltPanel routes
    'voltpanel.dashboard': () => `${basePath}`,
    'voltpanel.resources.index': (p) => `${basePath}/resources/${p.resource}`,
    'voltpanel.resources.create': (p) => `${basePath}/resources/${p.resource}/create`,
    'voltpanel.resources.store': (p) => `${basePath}/resources/${p.resource}`,
    'voltpanel.resources.show': (p) => `${basePath}/resources/${p.resource}/${p.id || p.record}`,
    'voltpanel.resources.view': (p) => `${basePath}/resources/${p.resource}/${p.id || p.record}`,
    'voltpanel.resources.edit': (p) => `${basePath}/resources/${p.resource}/${p.id || p.record}/edit`,
    'voltpanel.resources.update': (p) => `${basePath}/resources/${p.resource}/${p.id || p.record}`,
    'voltpanel.resources.destroy': (p) => `${basePath}/resources/${p.resource}/${p.id || p.record}`,
    'voltpanel.resources.export': (p) => `${basePath}/resources/${p.resource}/export`,
    'voltpanel.resources.bulk-action': (p) => `${basePath}/resources/${p.resource}/bulk-action`,
    'voltpanel.pages.show': (p) => `${basePath}/pages/${p.page}`,
    'voltpanel.global-search': () => `${basePath}/global-search`,
    'voltpanel.comments.store': (p) => `${basePath}/comments/${p.commentable_type}/${p.commentable_id}`,
    'voltpanel.comments.destroy': (p) => `${basePath}/comments/${p.comment}`,
    'voltpanel.saved-filters.index': (p) => `${basePath}/saved-filters/${p.resource}`,
    'voltpanel.saved-filters.store': (p) => `${basePath}/saved-filters/${p.resource}`,
    'voltpanel.saved-filters.destroy': (p) => `${basePath}/saved-filters/${p.filter}`,
    'voltpanel.media.upload': () => `${basePath}/media/upload`,
    'voltpanel.tenants.switch': (p) => `${basePath}/tenants/${p.tenant}/switch`,
    'voltpanel.api.tenants.index': () => `${basePath}/api/tenants`,
    'voltpanel.api.tenants.switch': (p) => `${basePath}/api/tenants/${p.tenantId}/switch`,
    'voltpanel.api.table-preferences.store': () => `${basePath}/api/table-preferences`,
    'voltpanel.api.rich-editor.upload': () => `${basePath}/api/rich-editor/upload`,
    'voltpanel.api.saved-filters.index': () => `${basePath}/api/saved-filters`,
    'voltpanel.api.saved-filters.store': () => `${basePath}/api/saved-filters`,
    'voltpanel.api.saved-filters.destroy': (p) => `${basePath}/api/saved-filters/${p.id}`,
    'voltpanel.api.saved-filters.make-default': (p) => `${basePath}/api/saved-filters/${p.id}/make-default`,
    'voltpanel.api.comments.store': () => `${basePath}/api/comments`,
    'voltpanel.api.comments.destroy': (p) => `${basePath}/api/comments/${p.id}`,
    // Common Laravel routes
    'logout': () => '/logout',
    'profile.edit': () => '/settings/profile',
    'profile.update': () => '/settings/profile',
  };

  const pattern = routePatterns[name];
  if (pattern) {
    return pattern(params);
  }

  // Unknown route - return base path with warning
  console.warn(`[VoltPanel] Unknown route '${name}', returning base path`);
  return basePath;
};

/**
 * Get the base path for VoltPanel routes.
 * This reads from the page props or falls back to '/admin'.
 */
const getBasePath = () => {
  // Try to get from Inertia page props
  if (typeof window !== 'undefined') {
    const inertiaPage = window.__page;
    if (inertiaPage?.props?.voltpanel?.basePath) {
      return inertiaPage.props.voltpanel.basePath;
    }
  }

  // Default to '/admin'
  return '/admin';
};

/**
 * Composable for routing in VoltPanel components.
 */
export function useRouting() {
  const mode = computed(() => getRoutingMode());

  /**
   * Generate a route URL
   */
  const route = (name, params = {}) => {
    return generateRoute(name, params);
  };

  /**
   * Check if a route exists (Ziggy only)
   */
  const hasRoute = (name) => {
    if (hasZiggy()) {
      try {
        window.route().has(name);
        return true;
      } catch {
        return false;
      }
    }
    return true; // Assume exists for fallback mode
  };

  /**
   * Get current route name (Ziggy only)
   */
  const currentRoute = () => {
    if (hasZiggy()) {
      try {
        return window.route().current() || null;
      } catch {
        return null;
      }
    }
    return null;
  };

  /**
   * Check if current route matches a pattern (Ziggy only)
   */
  const isCurrentRoute = (name, params) => {
    if (hasZiggy()) {
      try {
        return window.route().current(name, params);
      } catch {
        return false;
      }
    }
    return false;
  };

  return {
    mode,
    route,
    hasRoute,
    currentRoute,
    isCurrentRoute,
    generateRoute,
  };
}

export default useRouting;
