<template>
    <div class="bg-card rounded-lg shadow">
        <div class="p-4 border-b border-border">
            <h3 class="text-lg font-semibold text-card-foreground">
                Recent Activity
            </h3>
        </div>
        <div class="divide-y divide-border">
            <div
                v-for="activity in widget.data.activities"
                :key="activity.id"
                class="p-4 hover:bg-accent transition-colors"
            >
                <div class="flex items-start space-x-3">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                        :class="getEventColor(activity.event)"
                    >
                        <span class="text-sm">{{ getEventIcon(activity.event) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-card-foreground">
                            <span class="font-medium">{{ activity.causer }}</span>
                            {{ activity.description }}
                        </p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ activity.created_at }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-if="widget.data.activities.length === 0" class="p-8 text-center text-muted-foreground">
                No recent activity
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    widget: {
        type: Object,
        required: true,
    },
});

const getEventIcon = (event) => {
    const icons = {
        created: '✨',
        updated: '✏️',
        deleted: '🗑️',
        restored: '♻️',
    };
    return icons[event] || '📝';
};

const getEventColor = (event) => {
    const colors = {
        created: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        updated: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        deleted: 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300',
        restored: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
    };
    return colors[event] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
};
</script>
