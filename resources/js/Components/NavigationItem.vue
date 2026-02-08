<template>
    <div class="relative" @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" ref="itemRef">
        <div class="flex items-center">
            <Link
                :href="item.url"
                @click="handleLinkClick"
                class="flex items-center flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200"
                :class="[
                    isActive
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : hasActiveChild
                        ? 'bg-accent/50 text-accent-foreground'
                        : 'hover:bg-accent hover:text-accent-foreground',
                    collapsed ? 'justify-center' : ''
                ]"
                :title="collapsed ? item.label : undefined"
            >
                <Icon v-if="item.icon" :name="item.icon" class="w-5 h-5" :class="[isActive || hasActiveChild ? 'opacity-100' : 'opacity-70', !collapsed && 'mr-3']" />
                <span v-if="!collapsed">{{ item.label }}</span>
                <span v-if="item.badge && !collapsed" class="ml-auto px-2 py-0.5 text-xs rounded-full bg-muted text-muted-foreground font-semibold">
                    {{ item.badge }}
                </span>
            </Link>

            <!-- Collapse/Expand toggle button -->
            <button
                v-if="item.children && item.children.length > 0 && !collapsed"
                @click="isExpanded = !isExpanded"
                class="p-2 text-muted-foreground hover:text-foreground transition-colors"
                :aria-label="isExpanded ? 'Collapse' : 'Expand'"
            >
                <svg
                    class="w-4 h-4 transition-transform"
                    :class="{ 'rotate-90': isExpanded }"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </div>

        <!-- Children items (expanded sidebar) -->
        <div
            v-if="item.children && item.children.length > 0 && isExpanded && !collapsed"
            class="ml-6 mt-1 space-y-1"
        >
            <NavigationItem
                v-for="child in item.children"
                :key="child.label"
                :item="child"
                :collapsed="collapsed"
                @navigate="emit('navigate')"
            />
        </div>

        <!-- Children popover (collapsed sidebar) - Teleported to body -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div
                    v-if="item.children && item.children.length > 0 && collapsed && showPopover"
                    @mouseenter="handlePopoverEnter"
                    @mouseleave="handlePopoverLeave"
                    class="fixed w-56 rounded-md shadow-lg bg-popover border py-1 z-50"
                    :style="popoverStyle"
                >
                    <div class="px-3 py-2 text-xs font-semibold text-muted-foreground border-b">
                        {{ item.label }}
                    </div>
                    <div class="py-1">
                        <Link
                            v-for="child in item.children"
                            :key="child.label"
                            :href="child.url"
                            @click="handleLinkClick"
                            class="flex items-center px-3 py-2 text-sm hover:bg-accent hover:text-accent-foreground transition-colors"
                            :class="[
                                (child.active !== undefined ? child.active : page.url.startsWith(child.url))
                                    ? 'bg-accent text-accent-foreground'
                                    : ''
                            ]"
                        >
                            <Icon v-if="child.icon" :name="child.icon" class="w-4 h-4 mr-3 opacity-70" />
                            <span>{{ child.label }}</span>
                        </Link>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from './Icon.vue';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    collapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['navigate']);

const page = usePage();
const isExpanded = ref(true); // Default to expanded
const showPopover = ref(false);
const itemRef = ref(null);
let popoverTimeout = null;

const isActive = computed(() => {
    // Use the active property from the backend if available
    if (props.item.active !== undefined) {
        return props.item.active;
    }
    // Fallback to URL comparison
    return page.url.startsWith(props.item.url);
});

const hasActiveChild = computed(() => {
    if (!props.item.children || props.item.children.length === 0) {
        return false;
    }
    return props.item.children.some(child => {
        if (child.active !== undefined) {
            return child.active;
        }
        return page.url.startsWith(child.url);
    });
});

const popoverStyle = computed(() => {
    if (!itemRef.value) return {};

    const rect = itemRef.value.getBoundingClientRect();
    return {
        top: `${rect.top}px`,
        left: `${rect.right + 8}px`, // 8px gap from sidebar
    };
});

const handleLinkClick = () => {
    emit('navigate');
};

const handleMouseEnter = () => {
    if (props.collapsed && props.item.children && props.item.children.length > 0) {
        clearTimeout(popoverTimeout);
        popoverTimeout = setTimeout(() => {
            showPopover.value = true;
        }, 200); // Small delay to prevent accidental triggers
    }
};

const handleMouseLeave = () => {
    clearTimeout(popoverTimeout);
    popoverTimeout = setTimeout(() => {
        showPopover.value = false;
    }, 300); // Delay to allow moving to popover
};

const handlePopoverEnter = () => {
    clearTimeout(popoverTimeout);
};

const handlePopoverLeave = () => {
    clearTimeout(popoverTimeout);
    showPopover.value = false;
};
</script>
