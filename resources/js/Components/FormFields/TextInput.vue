<template>
    <input
        :id="component.name"
        :type="component.inputType || 'text'"
        :placeholder="component.placeholder"
        :required="component.required"
        :maxlength="component.maxLength"
        :minlength="component.minLength"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400"
    />
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
