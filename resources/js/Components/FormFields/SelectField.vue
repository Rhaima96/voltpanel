<template>
    <!-- Searchable Select -->
    <div v-if="component.searchable" class="relative">
        <!-- Selected Value Display -->
        <div
            class="min-h-[42px] w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 focus-within:ring-2 focus-within:ring-primary-500 dark:focus-within:ring-primary-400 cursor-pointer"
            @click="toggleDropdown"
        >
            <span v-if="modelValue && component.options[modelValue]" class="text-gray-900 dark:text-white">
                {{ component.options[modelValue] }}
            </span>
            <span v-else class="text-gray-500 dark:text-gray-400">Select an option...</span>
        </div>

        <!-- Dropdown Menu -->
        <div
            v-if="dropdownOpen"
            class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto"
        >
            <!-- Search Input -->
            <div class="sticky top-0 p-2 bg-white dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <input
                    ref="searchInput"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400"
                    @click.stop
                />
            </div>

            <!-- Options List -->
            <div class="py-1">
                <div
                    v-for="(label, value) in filteredOptions"
                    :key="value"
                    class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                    :class="{ 'bg-primary-50 dark:bg-primary-900': modelValue === value }"
                    @click="selectValue(value)"
                >
                    <span class="text-gray-900 dark:text-white">{{ label }}</span>
                </div>
                <div v-if="Object.keys(filteredOptions).length === 0" class="px-3 py-2 text-gray-500 dark:text-gray-400">
                    No options found
                </div>
            </div>
        </div>
    </div>

    <!-- Regular Select -->
    <select
        v-else
        :id="component.name"
        :required="component.required"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400"
    >
        <option value="">Select an option</option>
        <option
            v-for="(label, value) in component.options"
            :key="value"
            :value="value"
        >
            {{ label }}
        </option>
    </select>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [String, Number],
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const dropdownOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);

const filteredOptions = computed(() => {
    if (!props.component.options) return {};

    if (!searchQuery.value) {
        return props.component.options;
    }

    const search = searchQuery.value.toLowerCase();
    return Object.fromEntries(
        Object.entries(props.component.options).filter(([_, label]) =>
            label.toLowerCase().includes(search)
        )
    );
});

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
    if (dropdownOpen.value) {
        searchQuery.value = '';
        // Focus the search input when dropdown opens
        setTimeout(() => {
            searchInput.value?.focus();
        }, 100);
    }
};

const selectValue = (value) => {
    emit('update:modelValue', value);
    dropdownOpen.value = false;
    searchQuery.value = '';
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
