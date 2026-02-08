<template>
    <div class="space-y-2">
        <div
            v-for="(label, value) in component.options"
            :key="value"
            class="flex items-center"
        >
            <input
                :id="`${component.name}-${value}`"
                type="radio"
                :name="component.name"
                :value="value"
                :checked="modelValue === value"
                @change="$emit('update:modelValue', value)"
                class="h-4 w-4 border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 dark:bg-gray-700"
            />
            <label
                :for="`${component.name}-${value}`"
                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
            >
                {{ label }}
            </label>
        </div>
    </div>
</template>

<script setup>
defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [String, Number],
        default: null,
    },
});

defineEmits(['update:modelValue']);
</script>
