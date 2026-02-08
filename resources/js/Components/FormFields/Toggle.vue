<template>
    <div class="flex items-center">
        <button
            type="button"
            :aria-pressed="modelValue"
            @click="$emit('update:modelValue', !modelValue)"
            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2"
            :class="modelValue ? 'bg-blue-600 focus:ring-blue-500' : 'bg-gray-200 dark:bg-gray-700 focus:ring-gray-400'"
        >
            <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="modelValue ? 'translate-x-5' : 'translate-x-0'"
            ></span>
        </button>
        <label
            v-if="component.label"
            @click="$emit('update:modelValue', !modelValue)"
            class="ml-3 text-sm text-gray-700 dark:text-gray-300 cursor-pointer"
        >
            {{ component.label }}
            <span v-if="component.required" class="text-red-500 ml-1">*</span>
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
