<template>
    <div class="min-h-screen bg-background">
        <!-- Mobile Sidebar Backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
                @click="sidebarOpen = false"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 bg-card border-r shadow-sm transform transition-all duration-300 lg:translate-x-0"
            :class="[
                sidebarCollapsed ? 'w-16' : 'w-64',
                { '-translate-x-full': !sidebarOpen }
            ]"
        >
            <!-- Brand -->
            <div class="relative h-16 px-4 border-b" :class="sidebarCollapsed ? 'flex items-center justify-center' : 'flex items-center justify-between'">
                <div v-if="!sidebarCollapsed" class="flex items-center space-x-3 flex-1 min-w-0">
                    <img
                        v-if="branding.logo"
                        :src="branding.logo"
                        :alt="branding.name"
                        class="h-12 w-auto max-w-full"
                    />
                    <h1
                        v-else
                        class="text-xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent whitespace-nowrap"
                    >
                        {{ branding.name }}
                    </h1>
                </div>
                <div v-else class="flex items-center justify-center w-full">
                    <img
                        v-if="branding.logo"
                        :src="branding.logo"
                        :alt="branding.name"
                        class="h-10 w-10 object-contain"
                    />
                    <div v-else class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                        <span class="text-primary-foreground font-bold text-lg">{{ branding.name.charAt(0) }}</span>
                    </div>
                </div>

                <!-- Collapse Toggle (Desktop only) -->
                <button
                    @click="toggleSidebarCollapse"
                    class="hidden lg:flex p-1.5 rounded-md hover:bg-accent transition-colors flex-shrink-0"
                    :class="sidebarCollapsed ? 'absolute top-1/2 right-1 -translate-y-1/2 opacity-0 hover:opacity-100' : ''"
                    :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                >
                    <svg class="w-4 h-4 text-muted-foreground transition-transform" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="p-2 space-y-4 overflow-y-auto h-[calc(100vh-4rem)]">
                <!-- Ungrouped Items -->
                <div v-if="ungroupedNavigation.length > 0" class="space-y-1">
                    <NavigationItem
                        v-for="item in ungroupedNavigation"
                        :key="item.label"
                        :item="item"
                        :collapsed="sidebarCollapsed"
                        @navigate="sidebarOpen = false"
                    />
                </div>

                <!-- Grouped Items -->
                <div v-for="(items, groupName) in groupedNavigation" :key="groupName" class="space-y-1">
                    <div v-if="!sidebarCollapsed" class="px-3 py-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        {{ groupName }}
                    </div>
                    <div v-else class="h-px bg-border mx-2 my-2"></div>
                    <NavigationItem
                        v-for="item in items"
                        :key="item.label"
                        :item="item"
                        :collapsed="sidebarCollapsed"
                        @navigate="sidebarOpen = false"
                    />
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="transition-all duration-300" :class="sidebarCollapsed ? 'lg:pl-16' : 'lg:pl-64'">
            <!-- Header -->
            <header class="sticky top-0 z-40 bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/60 border-b shadow-sm">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-md text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-4 ml-auto">
                        <!-- Tenant Switcher -->
                        <TenantSwitcher
                            v-if="multiTenancyEnabled"
                            :initial-tenant="currentTenant"
                        />

                        <!-- Global Search -->
                        <GlobalSearch />

                        <!-- Dark Mode Toggle -->
                        <button
                            @click="toggleDarkMode"
                            class="p-2 rounded-md text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors"
                            title="Toggle dark mode"
                        >
                            <svg v-if="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- User Menu -->
                        <div v-if="user" class="relative" ref="userMenuRef">
                            <button
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center space-x-2 p-1.5 rounded-full hover:ring-2 hover:ring-ring hover:ring-offset-2 transition-all"
                            >
                                <img
                                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=4F46E5&color=fff`"
                                    class="w-8 h-8 rounded-full ring-2 ring-border"
                                    :alt="user.name"
                                />
                            </button>

                            <!-- Dropdown Menu -->
                            <Transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div
                                    v-if="userMenuOpen"
                                    class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-popover border py-1"
                                >
                                    <div class="px-4 py-3 border-b">
                                        <p class="text-sm font-medium">{{ user.name }}</p>
                                        <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                                    </div>

                                    <button
                                        @click="logout"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-accent hover:text-accent-foreground rounded-sm mx-1 transition-colors text-destructive"
                                    >
                                        Logout
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 space-y-6">
                <slot />
            </main>
        </div>

        <!-- Toast Notifications -->
        <Toast />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import NavigationItem from '../../components/VoltPanel/NavigationItem.vue';
import Toast from '../../components/VoltPanel/Toast.vue';
import GlobalSearch from '../../components/VoltPanel/GlobalSearch.vue';
import TenantSwitcher from '../../components/VoltPanel/TenantSwitcher.vue';
import { useDarkMode } from '../../composables/VoltPanel/useDarkMode';
import { useTheme } from '../../composables/VoltPanel/useTheme';
import { useRouting } from '../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);

// Toggle sidebar collapse
const toggleSidebarCollapse = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value.toString());
};

// Dark mode
const { isDark, toggle: toggleDarkMode } = useDarkMode();

// Theme
const { theme, applyTheme } = useTheme();

// Get VoltPanel data from shared Inertia props
const page = usePage();
const voltpanel = computed(() => page.props.voltpanel || {});
const branding = computed(() => voltpanel.value.branding || { name: 'VoltPanel' });
const navigation = computed(() => voltpanel.value.navigation || []);
const user = computed(() => page.props.auth?.user || null);
const multiTenancyEnabled = computed(() => voltpanel.value.multiTenancyEnabled || false);
const currentTenant = computed(() => voltpanel.value.currentTenant || null);

// Group navigation items
const ungroupedNavigation = computed(() => {
    const items = navigation.value.filter(item => (!item.group || item.group === '') && !item.parent);

    // Add children to each item
    items.forEach(item => {
        const children = navigation.value.filter(child => child.parent && child.parent.includes(item.label));
        if (children.length > 0) {
            item.children = children;
        }
    });

    return items;
});

const groupedNavigation = computed(() => {
    const groups = {};
    navigation.value
        .filter(item => item.group && item.group !== '' && !item.parent)
        .forEach(item => {
            if (!groups[item.group]) {
                groups[item.group] = [];
            }

            // Add children to this item
            const children = navigation.value.filter(child => child.parent && child.parent.includes(item.label));
            if (children.length > 0) {
                item.children = children;
            }

            groups[item.group].push(item);
        });
    return groups;
});

// Close user menu when clicking outside
const handleClickOutside = (event) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);

    // Load sidebar collapse state from localStorage
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState !== null) {
        sidebarCollapsed.value = savedState === 'true';
    }

    // Explicitly apply theme on mount
    applyTheme();
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Logout function
const logout = () => {
    router.post(route('logout'));
};
</script>
