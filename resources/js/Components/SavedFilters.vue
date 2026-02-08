<template>
  <div class="flex items-center gap-2">
    <!-- Saved Filters Dropdown -->
    <div class="relative" ref="dropdownRef">
      <button
        @click="isOpen = !isOpen"
        type="button"
        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
        </svg>
        {{ currentFilterName || 'Filters' }}
      </button>

      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50"
      >
        <div class="p-4 space-y-3">
          <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Saved Filters</h3>
            <button
              @click="showSaveDialog = true; isOpen = false"
              type="button"
              class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              Save Current
            </button>
          </div>

          <div class="space-y-1 max-h-64 overflow-y-auto">
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              type="button"
              class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Clear All Filters
            </button>

            <div
              v-for="filter in savedFilters"
              :key="filter.id"
              class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <button
                @click="loadFilter(filter)"
                type="button"
                class="flex-1 text-left text-sm text-gray-700 dark:text-gray-300"
              >
                <div class="flex items-center gap-2">
                  {{ filter.name }}
                  <span v-if="filter.is_default" class="text-xs text-indigo-600 dark:text-indigo-400">(Default)</span>
                  <span v-if="filter.is_public" class="text-xs text-gray-500">(Public)</span>
                </div>
              </button>

              <div class="flex items-center gap-1">
                <button
                  v-if="!filter.is_default"
                  @click="makeDefault(filter.id)"
                  type="button"
                  title="Make default"
                  class="p-1 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                  </svg>
                </button>

                <button
                  @click="confirmDeleteFilter(filter.id, filter.name)"
                  type="button"
                  title="Delete"
                  class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>

            <div v-if="savedFilters.length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 text-center">
              No saved filters yet
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Filter Dialog -->
    <div
      v-if="showSaveDialog"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="showSaveDialog = false"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Save Filter</h3>

        <form @submit.prevent="saveFilter" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Filter Name
            </label>
            <input
              v-model="saveForm.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
              placeholder="My Custom Filter"
            />
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="saveForm.is_public"
              type="checkbox"
              id="is_public"
              class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
            <label for="is_public" class="text-sm text-gray-700 dark:text-gray-300">
              Share with team
            </label>
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="saveForm.is_default"
              type="checkbox"
              id="is_default"
              class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
            <label for="is_default" class="text-sm text-gray-700 dark:text-gray-300">
              Make this my default filter
            </label>
          </div>

          <div class="flex items-center gap-3 pt-4">
            <button
              type="button"
              @click="showSaveDialog = false"
              class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmationModal
      :show="deleteConfirmation.show"
      :title="deleteConfirmation.title"
      :message="deleteConfirmation.message"
      confirm-text="Delete"
      cancel-text="Cancel"
      type="danger"
      @confirm="performDelete"
      @close="closeDeleteConfirmation"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from '../../composables/VoltPanel/useToast'
import { useRouting } from '../../composables/VoltPanel/useRouting'
import ConfirmationModal from './ConfirmationModal.vue'

const { route } = useRouting()

const toast = useToast()

const props = defineProps({
  resource: {
    type: String,
    required: true
  },
  currentFilters: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['load-filter', 'clear-filters'])

const isOpen = ref(false)
const showSaveDialog = ref(false)
const savedFilters = ref([])
const saveForm = ref({
  name: '',
  is_public: false,
  is_default: false
})
const deleteConfirmation = ref({
  show: false,
  filterId: null,
  filterName: '',
  title: 'Delete Filter',
  message: ''
})
const dropdownRef = ref(null)

const hasActiveFilters = computed(() => {
  return Object.keys(props.currentFilters).length > 0
})

const currentFilterName = computed(() => {
  const defaultFilter = savedFilters.value.find(f => f.is_default)
  const matchingFilter = savedFilters.value.find(f =>
    JSON.stringify(f.filters) === JSON.stringify(props.currentFilters)
  )

  return matchingFilter?.name || (defaultFilter && !hasActiveFilters.value ? defaultFilter.name : null)
})

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  loadSavedFilters()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

async function loadSavedFilters() {
  try {
    const response = await axios.get(route('voltpanel.api.saved-filters.index'), {
      params: { resource: props.resource }
    })
    savedFilters.value = response.data
  } catch (error) {
    console.error('Failed to load saved filters:', error)
  }
}

function loadFilter(filter) {
  emit('load-filter', filter.filters)
  isOpen.value = false
}

function clearFilters() {
  emit('clear-filters')
  isOpen.value = false
}

async function saveFilter() {
  try {
    const response = await axios.post(route('voltpanel.api.saved-filters.store'), {
      resource: props.resource,
      name: saveForm.value.name,
      filters: props.currentFilters,
      is_public: saveForm.value.is_public,
      is_default: saveForm.value.is_default
    })

    await loadSavedFilters()
    showSaveDialog.value = false
    saveForm.value = { name: '', is_public: false, is_default: false }
    toast.success('Filter saved successfully')
  } catch (error) {
    console.error('Failed to save filter:', error)
    toast.error('Failed to save filter: ' + (error.response?.data?.message || error.message))
  }
}

function confirmDeleteFilter(id, name) {
  deleteConfirmation.value = {
    show: true,
    filterId: id,
    filterName: name,
    title: 'Delete Filter',
    message: `Are you sure you want to delete "${name}"? This action cannot be undone.`
  }
}

async function performDelete() {
  try {
    await axios.delete(route('voltpanel.api.saved-filters.destroy', { id: deleteConfirmation.value.filterId }))
    await loadSavedFilters()
    closeDeleteConfirmation()
    toast.success('Filter deleted successfully')
  } catch (error) {
    console.error('Failed to delete filter:', error)
    toast.error('Failed to delete filter: ' + (error.response?.data?.message || error.message))
  }
}

function closeDeleteConfirmation() {
  deleteConfirmation.value = {
    show: false,
    filterId: null,
    filterName: '',
    title: 'Delete Filter',
    message: ''
  }
}

async function makeDefault(id) {
  try {
    await axios.post(route('voltpanel.api.saved-filters.make-default', { id }))
    await loadSavedFilters()
    toast.success('Filter set as default')
  } catch (error) {
    console.error('Failed to make filter default:', error)
    toast.error('Failed to make filter default: ' + (error.response?.data?.message || error.message))
  }
}
</script>
