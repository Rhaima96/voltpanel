<template>
    <div class="relative">
        <input
            :id="component.name"
            type="date"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :required="component.required"
            :min="component.minDate"
            :max="component.maxDate"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-base focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-70 [&::-webkit-calendar-picker-indicator]:hover:opacity-100"
        />
        <svg class="absolute right-10 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
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
