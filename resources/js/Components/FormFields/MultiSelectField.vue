<template>
    <div class="relative">
        <!-- Selected Tags Display -->
        <div
            class="min-h-[42px] w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 focus-within:ring-2 focus-within:ring-primary-500 dark:focus-within:ring-primary-400 cursor-pointer"
            @click="toggleDropdown"
        >
            <div class="flex flex-wrap gap-2">
                <template v-if="Array.isArray(modelValue) && modelValue.length > 0">
                    <span
                        v-for="selectedValue in modelValue"
                        :key="selectedValue"
                        class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded text-sm"
                    >
                        {{ component.options[selectedValue] }}
                        <button
                            type="button"
                            @click.stop="removeValue(selectedValue)"
                            class="hover:text-primary-900 dark:hover:text-primary-100"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                </template>
                <span v-else class="text-gray-500 dark:text-gray-400">Select options...</span>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div
            v-if="dropdownOpen"
            class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto"
        >
            <!-- Search Input -->
            <div class="sticky top-0 p-2 bg-white dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400"
                    @click.stop
                />
            </div>

            <!-- Options List -->
            <div class="py-1">
                <label
                    v-for="(label, value) in filteredOptions"
                    :key="value"
                    class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                    @click.stop
                >
                    <input
                        type="checkbox"
                        :value="value"
                        :checked="isValueSelected(value)"
                        @change="toggleValue(value)"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                    />
                    <span class="ml-2 text-gray-900 dark:text-white">{{ label }}</span>
                </label>
                <div v-if="Object.keys(filteredOptions).length === 0" class="px-3 py-2 text-gray-500 dark:text-gray-400">
                    No options found
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue']);

const dropdownOpen = ref(false);
const searchQuery = ref('');

const filteredOptions = computed(() => {
    if (!props.component.options) return {};

    const selectedValues = Array.isArray(props.modelValue) ? props.modelValue : [];

    // Filter out already selected values
    let availableOptions = Object.fromEntries(
        Object.entries(props.component.options).filter(([value, _]) =>
            !selectedValues.includes(value) && !selectedValues.includes(Number(value))
        )
    );

    // Apply search filter
    if (searchQuery.value) {
        const search = searchQuery.value.toLowerCase();
        availableOptions = Object.fromEntries(
            Object.entries(availableOptions).filter(([_, label]) =>
                label.toLowerCase().includes(search)
            )
        );
    }

    return availableOptions;
});

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
    if (dropdownOpen.value) {
        searchQuery.value = '';
    }
};

const isValueSelected = (value) => {
    if (!Array.isArray(props.modelValue)) return false;
    return props.modelValue.includes(value);
};

const toggleValue = (value) => {
    const currentValue = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    const index = currentValue.indexOf(value);

    if (index > -1) {
        currentValue.splice(index, 1);
    } else {
        currentValue.push(value);
    }

    emit('update:modelValue', currentValue);
};

const removeValue = (value) => {
    const currentValue = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    const index = currentValue.indexOf(value);

    if (index > -1) {
        currentValue.splice(index, 1);
        emit('update:modelValue', currentValue);
    }
};

const handleClickOutside = (event) => {
    const clickedElement = event.target;
    const relativeParent = clickedElement.closest('.relative');

    if (dropdownOpen.value && !relativeParent) {
        dropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
