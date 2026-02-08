<template>
    <textarea
        :id="component.name"
        :placeholder="component.placeholder"
        :required="component.required"
        :rows="component.rows || 4"
        :maxlength="component.maxLength"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400"
    ></textarea>
</template>

<script setup>
defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue']);
</script>
