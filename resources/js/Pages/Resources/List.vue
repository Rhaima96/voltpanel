<template>
    <PanelLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ title }}
            </h1>
            <div class="flex items-center gap-3">
                <!-- Column Visibility Toggle -->
                <ColumnVisibilityToggle
                    v-if="hasTablePreferences"
                    :columns="table.columns"
                    :table-identifier="`${resource}-table`"
                    :persist-enabled="true"
                    @update:visible-columns="handleVisibleColumnsUpdate"
                />

                <!-- Export Dropdown -->
                <div v-if="canExport" class="relative">
                    <button
                        @click="exportMenuOpen = !exportMenuOpen"
                        @blur="handleExportBlur"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        v-show="exportMenuOpen"
                        class="absolute right-0 mt-2 w-48 rounded-md border bg-popover p-1 text-popover-foreground shadow-md z-50"
                    >
                        <button
                            v-if="exportFormats.includes('csv')"
                            @mousedown.prevent="exportData('csv')"
                            class="relative flex cursor-pointer select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground w-full"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export as CSV
                        </button>
                        <button
                            v-if="exportFormats.includes('xlsx')"
                            @mousedown.prevent="exportData('xlsx')"
                            class="relative flex cursor-pointer select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground w-full"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Export as Excel
                        </button>
                        <button
                            v-if="exportFormats.includes('pdf')"
                            @mousedown.prevent="exportData('pdf')"
                            class="relative flex cursor-pointer select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground w-full"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Export as PDF
                        </button>
                    </div>
                </div>

                <Link
                    v-if="canCreate"
                    :href="route('voltpanel.resources.create', { resource })"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-10 px-4 py-2 bg-primary text-primary-foreground hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                >
                    Create
                </Link>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div
            v-if="selectedRecords.length > 0 && table.bulkActions && table.bulkActions.length > 0"
            class="mb-4 bg-primary/10 border border-primary/20 rounded-lg p-4"
        >
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-foreground">
                    {{ selectedRecords.length }} record{{ selectedRecords.length !== 1 ? 's' : '' }} selected
                </span>
                <div class="flex items-center gap-2">
                    <button
                        v-for="bulkAction in table.bulkActions"
                        :key="bulkAction.name"
                        @click="executeBulkAction(bulkAction)"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-9 px-4 py-2"
                        :class="getBulkActionClasses(bulkAction.color)"
                    >
                        <svg v-if="bulkAction.icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(bulkAction.icon)"></path>
                        </svg>
                        {{ bulkAction.label }}
                    </button>
                    <button
                        @click="clearSelection"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-9 px-3 py-2 hover:bg-accent hover:text-accent-foreground"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-card text-card-foreground rounded-lg shadow overflow-hidden border">
            <!-- Search Bar -->
            <div v-if="table.searchable" class="p-4 border-b">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search..."
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                />
            </div>

            <!-- Filters -->
            <div v-if="table.filters && table.filters.length > 0" class="p-4 border-b bg-muted/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-foreground">Filters</h3>
                    <SavedFilters
                        :resource="resource"
                        :current-filters="filterValues"
                        @load-filter="loadSavedFilter"
                        @clear-filters="clearFilters"
                    />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="filter in table.filters" :key="filter.name">
                        <label :for="`filter-${filter.name}`" class="block text-sm font-medium text-foreground mb-2">
                            {{ filter.label }}
                        </label>

                        <!-- Select Filter -->
                        <div v-if="filter.type === 'select'" class="relative">
                            <!-- Selected Value Display -->
                            <button
                                type="button"
                                :id="`filter-${filter.name}`"
                                @click="toggleFilterDropdown(filter.name)"
                                class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer hover:border-primary/50 hover:bg-accent/50 transition-colors"
                            >
                                <span v-if="filterValues[filter.name] && filter.options[filterValues[filter.name]]" class="text-foreground">
                                    {{ filter.options[filterValues[filter.name]] }}
                                </span>
                                <span v-else class="text-muted-foreground">All</span>
                                <svg
                                    class="w-4 h-4 text-muted-foreground transition-transform"
                                    :class="{ 'rotate-180': openFilterDropdown === filter.name }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="openFilterDropdown === filter.name"
                                class="absolute z-50 w-full mt-1 bg-popover border border-border rounded-md shadow-lg max-h-60 overflow-auto"
                            >
                                <!-- Search Input (if many options) -->
                                <div
                                    v-if="Object.keys(filter.options).length > 5"
                                    class="sticky top-0 p-2 bg-popover border-b border-border"
                                >
                                    <input
                                        :ref="el => filterSearchInputs[filter.name] = el"
                                        v-model="filterSearchValues[filter.name]"
                                        type="text"
                                        placeholder="Search..."
                                        class="w-full px-3 py-1.5 text-sm border border-input rounded-md bg-background text-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        @click.stop
                                    />
                                </div>

                                <!-- Options List -->
                                <div class="py-1">
                                    <!-- All option -->
                                    <div
                                        class="px-3 py-2 hover:bg-accent cursor-pointer transition-colors"
                                        :class="{ 'bg-primary/10 text-primary font-medium': !filterValues[filter.name] }"
                                        @click="selectFilterValue(filter.name, '')"
                                    >
                                        <span>All</span>
                                    </div>
                                    <!-- Regular options -->
                                    <div
                                        v-for="(label, value) in getFilteredOptions(filter)"
                                        :key="value"
                                        class="px-3 py-2 hover:bg-accent cursor-pointer transition-colors"
                                        :class="{ 'bg-primary/10 text-primary font-medium': filterValues[filter.name] === value }"
                                        @click="selectFilterValue(filter.name, value)"
                                    >
                                        <span>{{ label }}</span>
                                    </div>
                                    <div
                                        v-if="Object.keys(getFilteredOptions(filter)).length === 0"
                                        class="px-3 py-2 text-muted-foreground text-sm"
                                    >
                                        No options found
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ternary Filter -->
                        <div v-else-if="filter.type === 'ternary'" class="relative">
                            <!-- Selected Value Display -->
                            <button
                                type="button"
                                :id="`filter-${filter.name}`"
                                @click="toggleFilterDropdown(filter.name)"
                                class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer hover:border-primary/50 hover:bg-accent/50 transition-colors"
                            >
                                <span v-if="filterValues[filter.name] === '1'" class="text-foreground">
                                    {{ filter.trueLabel || 'Yes' }}
                                </span>
                                <span v-else-if="filterValues[filter.name] === '0'" class="text-foreground">
                                    {{ filter.falseLabel || 'No' }}
                                </span>
                                <span v-else class="text-muted-foreground">
                                    {{ filter.nullLabel || 'All' }}
                                </span>
                                <svg
                                    class="w-4 h-4 text-muted-foreground transition-transform"
                                    :class="{ 'rotate-180': openFilterDropdown === filter.name }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="openFilterDropdown === filter.name"
                                class="absolute z-50 w-full mt-1 bg-popover border border-border rounded-md shadow-lg"
                            >
                                <div class="py-1">
                                    <!-- All option -->
                                    <div
                                        class="px-3 py-2 hover:bg-accent cursor-pointer transition-colors"
                                        :class="{ 'bg-primary/10 text-primary font-medium': !filterValues[filter.name] || filterValues[filter.name] === '' }"
                                        @click="selectFilterValue(filter.name, '')"
                                    >
                                        <span>{{ filter.nullLabel || 'All' }}</span>
                                    </div>
                                    <!-- True option -->
                                    <div
                                        class="px-3 py-2 hover:bg-accent cursor-pointer transition-colors"
                                        :class="{ 'bg-primary/10 text-primary font-medium': filterValues[filter.name] === '1' }"
                                        @click="selectFilterValue(filter.name, '1')"
                                    >
                                        <span>{{ filter.trueLabel || 'Yes' }}</span>
                                    </div>
                                    <!-- False option -->
                                    <div
                                        class="px-3 py-2 hover:bg-accent cursor-pointer transition-colors"
                                        :class="{ 'bg-primary/10 text-primary font-medium': filterValues[filter.name] === '0' }"
                                        @click="selectFilterValue(filter.name, '0')"
                                    >
                                        <span>{{ filter.falseLabel || 'No' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="hasActiveFilters" class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="h-10 px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-accent rounded-md transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-muted/50">
                        <tr>
                            <!-- Bulk Selection Checkbox -->
                            <th v-if="table.bulkActions && table.bulkActions.length > 0" class="px-6 py-3 w-12">
                                <input
                                    type="checkbox"
                                    :checked="allRecordsSelected"
                                    @change="toggleAllRecords"
                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0"
                                />
                            </th>
                            <th
                                v-for="column in filteredColumns"
                                :key="column.name"
                                class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider"
                                :class="[
                                    column.alignment && `text-${column.alignment}`,
                                    column.sortable && 'cursor-pointer hover:bg-muted select-none'
                                ]"
                                @click="column.sortable && sortBy(column.name)"
                            >
                                <div class="flex items-center gap-2">
                                    <span>{{ column.label }}</span>
                                    <span v-if="column.sortable && filters?.sort === column.name" class="text-muted-foreground">
                                        <svg v-if="filters?.direction === 'asc'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5 10l5-5 5 5H5z"/>
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M15 10l-5 5-5-5h10z"/>
                                        </svg>
                                    </span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="record in records.data"
                            :key="record.id"
                            class="hover:bg-muted/50 transition-colors"
                            :class="{ 'bg-primary/5': isRecordSelected(record.id) }"
                        >
                            <!-- Bulk Selection Checkbox -->
                            <td v-if="table.bulkActions && table.bulkActions.length > 0" class="px-6 py-4 w-12">
                                <input
                                    type="checkbox"
                                    :checked="isRecordSelected(record.id)"
                                    @change="toggleRecord(record.id)"
                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0"
                                />
                            </td>
                            <td
                                v-for="column in filteredColumns"
                                :key="column.name"
                                class="px-6 py-4 whitespace-nowrap text-sm"
                                :class="column.alignment && `text-${column.alignment}`"
                            >
                                <TableCell :column="column" :record="record" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <TableAction
                                        v-for="(action, index) in table.actions"
                                        :key="index"
                                        :action="action"
                                        :record="record"
                                        :resource="resource"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                <Pagination :links="records.links" />
            </div>
        </div>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :show="confirmationModal.show"
            :title="confirmationModal.title"
            :message="confirmationModal.message"
            :confirm-text="confirmationModal.confirmText"
            :type="confirmationModal.type"
            @confirm="handleConfirmBulkAction"
            @close="closeConfirmationModal"
        />
    </PanelLayout>
</template>

<script setup>
import { ref, watch, computed, nextTick, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PanelLayout from '../../../layouts/VoltPanel/PanelLayout.vue';
import TableCell from '../../../components/VoltPanel/TableCell.vue';
import TableAction from '../../../components/VoltPanel/TableAction.vue';
import Pagination from '../../../components/VoltPanel/Pagination.vue';
import ConfirmationModal from '../../../components/VoltPanel/ConfirmationModal.vue';
import SavedFilters from '../../../components/VoltPanel/SavedFilters.vue';
import ColumnVisibilityToggle from '../../../components/VoltPanel/ColumnVisibilityToggle.vue';
import { useRouting } from '../../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const props = defineProps({
    resource: String,
    title: String,
    table: Object,
    records: Object,
    canCreate: Boolean,
    canExport: Boolean,
    exportFormats: Array,
    hasTablePreferences: Boolean,
    filters: Object,
});

// Initialize filter values with defaults from filter definitions
const initializeFilterValues = () => {
    const values = props.filters?.values || {};
    const defaults = {};

    // Set default values from filter definitions if not already set
    props.table.filters?.forEach(filter => {
        if (values[filter.name] === undefined || values[filter.name] === null) {
            defaults[filter.name] = filter.default || '';
        } else {
            defaults[filter.name] = values[filter.name];
        }
    });

    return defaults;
};

const search = ref(props.filters?.search || '');
const filterValues = ref(initializeFilterValues());
const exportMenuOpen = ref(false);
const selectedRecords = ref([]);
const visibleColumns = ref([]);
const confirmationModal = ref({
    show: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    type: 'danger',
    action: null,
});

// Filter dropdown state
const openFilterDropdown = ref(null);
const filterSearchValues = ref({});
const filterSearchInputs = ref({});

let searchTimeout = null;

// Initialize visible columns from localStorage
const initializeVisibleColumns = () => {
    if (!props.hasTablePreferences) {
        return props.table.columns.map(col => col.name);
    }

    const storageKey = `table_columns_${props.resource}-table`;
    const saved = localStorage.getItem(storageKey);

    if (saved) {
        try {
            const prefs = JSON.parse(saved);
            return prefs.visible || props.table.columns.map(col => col.name);
        } catch (e) {
            return props.table.columns.map(col => col.name);
        }
    }

    return props.table.columns.map(col => col.name);
};

visibleColumns.value = initializeVisibleColumns();

// Computed property for filtered columns based on visibility
const filteredColumns = computed(() => {
    if (!props.hasTablePreferences) {
        return props.table.columns.filter(c => !c.hidden);
    }

    return props.table.columns.filter(c =>
        !c.hidden && visibleColumns.value.includes(c.name)
    );
});

// Computed property to check if all records are selected
const allRecordsSelected = computed(() => {
    return props.records.data.length > 0 &&
           selectedRecords.value.length === props.records.data.length;
});

// Computed property to check if any filters are active
const hasActiveFilters = computed(() => {
    return Object.values(filterValues.value).some(value => value !== '' && value !== null);
});

// Watch search input and perform search with debouncing
watch(search, (value) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        router.get(
            route('voltpanel.resources.index', { resource: props.resource }),
            {
                search: value,
                sort: props.filters?.sort,
                direction: props.filters?.direction,
                filters: filterValues.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }, 300);
});

// Sort by column
const sortBy = (column) => {
    const currentSort = props.filters?.sort;
    const currentDirection = props.filters?.direction;

    let direction = 'asc';

    // Toggle direction if clicking the same column
    if (currentSort === column) {
        direction = currentDirection === 'asc' ? 'desc' : 'asc';
    }

    router.get(
        route('voltpanel.resources.index', { resource: props.resource }),
        {
            search: search.value,
            sort: column,
            direction: direction,
            filters: filterValues.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// Apply filters
const applyFilters = () => {
    router.get(
        route('voltpanel.resources.index', { resource: props.resource }),
        {
            search: search.value,
            sort: props.filters?.sort,
            direction: props.filters?.direction,
            filters: filterValues.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// Clear filters
const clearFilters = () => {
    // Reset to default values from filter definitions
    const defaults = {};
    props.table.filters?.forEach(filter => {
        defaults[filter.name] = filter.default || '';
    });
    filterValues.value = defaults;
    applyFilters();
};

// Load saved filter
const loadSavedFilter = (savedFilters) => {
    // Merge saved filters with current filter values
    filterValues.value = { ...filterValues.value, ...savedFilters };
    applyFilters();
};

// Handle visible columns update from ColumnVisibilityToggle
const handleVisibleColumnsUpdate = (columns) => {
    visibleColumns.value = columns;
};

// Toggle filter dropdown
const toggleFilterDropdown = (filterName) => {
    if (openFilterDropdown.value === filterName) {
        openFilterDropdown.value = null;
        filterSearchValues.value[filterName] = '';
    } else {
        openFilterDropdown.value = filterName;
        filterSearchValues.value[filterName] = '';
        // Focus search input if available
        nextTick(() => {
            if (filterSearchInputs.value[filterName]) {
                filterSearchInputs.value[filterName].focus();
            }
        });
    }
};

// Select filter value
const selectFilterValue = (filterName, value) => {
    filterValues.value[filterName] = value;
    openFilterDropdown.value = null;
    filterSearchValues.value[filterName] = '';
    applyFilters();
};

// Get filtered options for a filter (based on search)
const getFilteredOptions = (filter) => {
    const searchValue = filterSearchValues.value[filter.name] || '';
    if (!searchValue) {
        return filter.options;
    }

    const searchLower = searchValue.toLowerCase();
    return Object.fromEntries(
        Object.entries(filter.options).filter(([_, label]) =>
            label.toLowerCase().includes(searchLower)
        )
    );
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (openFilterDropdown.value) {
        const target = event.target;
        const dropdown = target.closest('.relative');
        if (!dropdown || !dropdown.querySelector(`#filter-${openFilterDropdown.value}`)) {
            openFilterDropdown.value = null;
        }
    }
};

const handleExportBlur = () => {
    setTimeout(() => {
        exportMenuOpen.value = false;
    }, 200);
};

const exportData = (format) => {
    exportMenuOpen.value = false;

    // Build query parameters
    const params = new URLSearchParams({
        format: format,
        search: search.value || '',
        sort: props.filters?.sort || '',
        direction: props.filters?.direction || 'asc',
    });

    // Add filter values to params
    Object.entries(filterValues.value).forEach(([key, value]) => {
        if (value !== '' && value !== null) {
            params.append(`filters[${key}]`, value);
        }
    });

    // Trigger download by navigating to the export URL
    window.location.href = route('voltpanel.resources.export', { resource: props.resource }) + `?${params.toString()}`;
};

// Bulk action methods
const isRecordSelected = (recordId) => {
    return selectedRecords.value.includes(recordId);
};

const toggleRecord = (recordId) => {
    const index = selectedRecords.value.indexOf(recordId);
    if (index > -1) {
        selectedRecords.value.splice(index, 1);
    } else {
        selectedRecords.value.push(recordId);
    }
};

const toggleAllRecords = () => {
    if (allRecordsSelected.value) {
        selectedRecords.value = [];
    } else {
        selectedRecords.value = props.records.data.map(record => record.id);
    }
};

const clearSelection = () => {
    selectedRecords.value = [];
};

const executeBulkAction = (bulkAction) => {
    if (bulkAction.requiresConfirmation) {
        // Show confirmation modal
        confirmationModal.value = {
            show: true,
            title: bulkAction.confirmationTitle || 'Confirm Action',
            message: bulkAction.confirmationText || `Are you sure you want to ${bulkAction.label.toLowerCase()} ${selectedRecords.value.length} record${selectedRecords.value.length !== 1 ? 's' : ''}?`,
            confirmText: bulkAction.label,
            type: getModalType(bulkAction.color),
            action: bulkAction,
        };
    } else {
        // Execute directly without confirmation
        performBulkAction(bulkAction);
    }
};

const performBulkAction = (bulkAction) => {
    // Send request to execute bulk action
    router.post(
        route('voltpanel.resources.bulk-action', { resource: props.resource }),
        {
            action: bulkAction.name,
            ids: selectedRecords.value,
        },
        {
            preserveState: false,
            onSuccess: () => {
                if (bulkAction.deselectRecordsAfterCompletion !== false) {
                    clearSelection();
                }
            },
        }
    );
};

const handleConfirmBulkAction = () => {
    if (confirmationModal.value.action) {
        performBulkAction(confirmationModal.value.action);
    }
    closeConfirmationModal();
};

const closeConfirmationModal = () => {
    confirmationModal.value = {
        show: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        type: 'danger',
        action: null,
    };
};

const getModalType = (color) => {
    const typeMap = {
        danger: 'danger',
        destructive: 'danger',
        warning: 'warning',
        primary: 'info',
        secondary: 'info',
        success: 'info',
    };
    return typeMap[color] || 'danger';
};

const getBulkActionClasses = (color) => {
    const colorClasses = {
        primary: 'bg-primary text-primary-foreground hover:opacity-90',
        secondary: 'bg-secondary text-secondary-foreground hover:opacity-90',
        danger: 'bg-destructive text-destructive-foreground hover:opacity-90',
        success: 'bg-green-600 text-white hover:bg-green-700',
        warning: 'bg-yellow-600 text-white hover:bg-yellow-700',
    };

    return colorClasses[color] || colorClasses.primary;
};

const getIconPath = (icon) => {
    const iconPaths = {
        trash: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        check: 'M5 13l4 4L19 7',
        archive: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        download: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
    };

    return iconPaths[icon] || '';
};

// Set up click outside listener for filter dropdowns
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
