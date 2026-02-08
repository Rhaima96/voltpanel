<template>
    <div>
        <!-- Text Column -->
        <span v-if="column.type === 'text'">
            {{ limitText(getFormattedValue()) }}
        </span>

        <!-- Badge Column -->
        <span
            v-else-if="column.type === 'badge'"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            :class="getBadgeClass()"
        >
            {{ getValue() }}
        </span>

        <!-- Boolean Column -->
        <div v-else-if="column.type === 'boolean'" class="flex items-center">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getValue() ? `bg-${column.trueColor}-100 text-${column.trueColor}-800` : `bg-${column.falseColor}-100 text-${column.falseColor}-800`"
            >
                {{ getValue() ? column.trueLabel : column.falseLabel }}
            </span>
        </div>

        <!-- Default -->
        <span v-else>
            {{ getFormattedValue() }}
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    column: {
        type: Object,
        required: true,
    },
    record: {
        type: Object,
        required: true,
    },
});

const getValue = () => {
    // Handle nested properties (e.g., 'category.name')
    if (props.column.name.includes('.')) {
        const keys = props.column.name.split('.');
        let value = props.record;
        for (const key of keys) {
            value = value?.[key];
            if (value === undefined || value === null) break;
        }
        return value;
    }
    return props.record[props.column.name];
};

const getFormattedValue = () => {
    const value = getValue();

    if (!value) return value;

    // Check if it's a datetime string (ISO 8601 format)
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}/.test(value)) {
        return formatDateTime(value);
    }

    return value;
};

const formatDateTime = (dateString) => {
    const date = new Date(dateString);

    if (isNaN(date.getTime())) {
        return dateString;
    }

    // Format: Dec 28, 2025 4:05 PM
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }) + ' ' + date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const limitText = (text) => {
    if (!props.column.limit || !text) return text;
    return text.length > props.column.limit
        ? text.substring(0, props.column.limit) + '...'
        : text;
};

const getBadgeClass = () => {
    const value = getValue();
    const colors = props.column.colors || {};
    const color = colors[value] || 'gray';
    return `bg-${color}-100 text-${color}-800 dark:bg-${color}-900 dark:text-${color}-300`;
};
</script>
