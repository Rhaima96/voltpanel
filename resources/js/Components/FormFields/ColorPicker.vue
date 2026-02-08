<template>
    <div class="space-y-2">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <input
                    :id="component.name"
                    type="color"
                    :value="modelValue || '#000000'"
                    @input="$emit('update:modelValue', $event.target.value)"
                    class="h-10 w-16 rounded-md border-2 border-gray-300 dark:border-gray-600 cursor-pointer"
                />
                <div
                    class="absolute inset-0 rounded-md pointer-events-none border-2 border-white shadow-sm"
                    :style="{ backgroundColor: modelValue || '#000000' }"
                ></div>
            </div>
            <input
                type="text"
                :value="modelValue || '#000000'"
                @input="$emit('update:modelValue', $event.target.value)"
                placeholder="#000000"
                pattern="^#[0-9A-Fa-f]{6}$"
                maxlength="7"
                class="flex h-10 flex-1 rounded-md border border-input bg-background px-3 py-2 text-sm font-mono ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 uppercase"
            />
        </div>
        <div v-if="modelValue" class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="font-medium">Preview:</span>
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600 shadow-sm" :style="{ backgroundColor: modelValue }"></div>
                <span class="font-mono">{{ modelValue }}</span>
            </div>
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
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue']);
</script>
