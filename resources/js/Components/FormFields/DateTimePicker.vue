<template>
    <div class="relative">
        <input
            :id="component.name"
            type="datetime-local"
            :value="formatDateTimeForInput(modelValue)"
            @input="$emit('update:modelValue', $event.target.value)"
            :required="component.required"
            :min="component.minDate"
            :max="component.maxDate"
            :step="component.step || 60"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-base focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-70 [&::-webkit-calendar-picker-indicator]:hover:opacity-100"
        />
        <svg class="absolute right-10 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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

const formatDateTimeForInput = (value) => {
    if (!value) return '';

    // If value is already in correct format (YYYY-MM-DDTHH:mm), return as is
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(value)) {
        return value.slice(0, 16); // Remove seconds if present
    }

    // Try to parse as Date
    try {
        const date = new Date(value);
        if (isNaN(date.getTime())) return '';

        // Format as YYYY-MM-DDTHH:mm
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    } catch (e) {
        return '';
    }
};
</script>
