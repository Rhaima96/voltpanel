<template>
    <div
        class="bg-card rounded-lg shadow p-6 hover:shadow-lg transition-shadow"
        :class="{ 'cursor-pointer': widget.data.url }"
        @click="widget.data.url && $inertia.visit(widget.data.url)"
    >
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-muted-foreground">
                    {{ widget.data.heading }}
                </p>
                <p class="mt-2 text-3xl font-semibold text-card-foreground">
                    {{ widget.data.value }}
                </p>
                <p v-if="widget.data.description" class="mt-2 text-sm text-muted-foreground">
                    {{ widget.data.description }}
                </p>
            </div>
            <div
                v-if="widget.data.icon"
                class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full"
                :class="getColorClasses(widget.data.color)"
            >
                <span
                    class="text-2xl"
                    v-html="widget.data.icon"
                ></span>
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

const getColorClasses = (color) => {
    const colorMap = {
        primary: 'bg-primary/10 text-primary dark:bg-primary/20',
        success: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        danger: 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300',
        warning: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
        info: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        indigo: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300',
        purple: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
        pink: 'bg-pink-100 text-pink-600 dark:bg-pink-900 dark:text-pink-300',
        cyan: 'bg-cyan-100 text-cyan-600 dark:bg-cyan-900 dark:text-cyan-300',
        gray: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
    };

    return colorMap[color] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
};
</script>
