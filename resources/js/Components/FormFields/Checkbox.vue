<template>
    <div class="flex items-center">
        <input
            :id="component.name"
            type="checkbox"
            :checked="modelValue"
            @change="$emit('update:modelValue', $event.target.checked)"
            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 dark:bg-gray-700"
        />
        <label
            v-if="component.label"
            :for="component.name"
            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
        >
            {{ component.label }}
        </label>
    </div>
</template>

<script setup>
defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>
