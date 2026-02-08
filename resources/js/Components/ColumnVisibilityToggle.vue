<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="isOpen = !isOpen"
      type="button"
      class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
      </svg>
      Columns
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50"
    >
      <div class="p-4 space-y-3">
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
          <h3 class="text-sm font-medium text-gray-900 dark:text-white">Toggle Columns</h3>
          <button
            @click="resetToDefault"
            type="button"
            class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
          >
            Reset
          </button>
        </div>

        <div class="space-y-2 max-h-96 overflow-y-auto">
          <div v-for="column in orderedColumns" :key="column.name" class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
            <label class="flex items-center flex-1 cursor-pointer">
              <input
                type="checkbox"
                :checked="isColumnVisible(column.name)"
                @change="toggleColumn(column.name)"
                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                {{ column.label }}
              </span>
            </label>
          </div>
        </div>

        <div v-if="persistEnabled" class="pt-2 border-t border-gray-200 dark:border-gray-700">
          <button
            @click="savePreferences"
            type="button"
            class="w-full px-3 py-2 text-sm font-medium bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all"
          >
            Save Preferences
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from '../../composables/VoltPanel/useToast'
import { useRouting } from '../../composables/VoltPanel/useRouting'

const { route } = useRouting()
const toast = useToast()

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  tableIdentifier: {
    type: String,
    required: true
  },
  defaultHiddenColumns: {
    type: Array,
    default: () => []
  },
  persistEnabled: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:visible-columns', 'update:column-order'])

const isOpen = ref(false)
const visibleColumns = ref([])
const orderedColumns = ref([...props.columns])
const dropdownRef = ref(null)

// Initialize from localStorage or default
const storageKey = `table_columns_${props.tableIdentifier}`
const savedPreferences = localStorage.getItem(storageKey)

if (savedPreferences) {
  const prefs = JSON.parse(savedPreferences)
  visibleColumns.value = prefs.visible || []

  if (prefs.order) {
    orderedColumns.value = prefs.order.map(name =>
      props.columns.find(col => col.name === name)
    ).filter(Boolean)
  }
} else {
  visibleColumns.value = props.columns
    .filter(col => !props.defaultHiddenColumns.includes(col.name))
    .map(col => col.name)
}

const isColumnVisible = (columnName) => {
  return visibleColumns.value.includes(columnName)
}

const toggleColumn = (columnName) => {
  if (isColumnVisible(columnName)) {
    visibleColumns.value = visibleColumns.value.filter(name => name !== columnName)
  } else {
    visibleColumns.value.push(columnName)
  }

  saveToLocalStorage()
  emit('update:visible-columns', visibleColumns.value)
}

const onReorder = () => {
  saveToLocalStorage()
  emit('update:column-order', orderedColumns.value.map(col => col.name))
}

const resetToDefault = () => {
  visibleColumns.value = props.columns
    .filter(col => !props.defaultHiddenColumns.includes(col.name))
    .map(col => col.name)

  orderedColumns.value = [...props.columns]

  saveToLocalStorage()
  emit('update:visible-columns', visibleColumns.value)
  emit('update:column-order', orderedColumns.value.map(col => col.name))
}

const saveToLocalStorage = () => {
  localStorage.setItem(storageKey, JSON.stringify({
    visible: visibleColumns.value,
    order: orderedColumns.value.map(col => col.name)
  }))
}

const savePreferences = async () => {
  if (!props.persistEnabled) return

  try {
    await axios.post(route('voltpanel.api.table-preferences.store'), {
      table_identifier: props.tableIdentifier,
      visible_columns: visibleColumns.value,
      hidden_columns: props.columns
        .filter(col => !visibleColumns.value.includes(col.name))
        .map(col => col.name),
      column_order: orderedColumns.value.map(col => col.name)
    })

    isOpen.value = false
    toast.success('Table preferences saved')
  } catch (error) {
    console.error('Failed to save table preferences:', error)
    toast.error('Failed to save table preferences: ' + (error.response?.data?.message || error.message))
  }
}

// Click outside to close
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
