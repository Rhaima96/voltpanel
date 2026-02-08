<template>
  <div class="relative" ref="dropdownRef" v-if="tenants.length > 1">
    <button
      @click="isOpen = !isOpen"
      type="button"
      class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
      </svg>
      <span>{{ currentTenant?.name || 'Select Tenant' }}</span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50"
    >
      <div class="p-2">
        <div class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
          Available Tenants
        </div>

        <div class="space-y-1">
          <button
            v-for="tenant in tenants"
            :key="tenant.id"
            @click="switchTenant(tenant.id)"
            type="button"
            :class="[
              'w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg',
              currentTenant?.id === tenant.id
                ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
            ]"
          >
            <div class="flex items-center gap-2">
              <div
                :class="[
                  'w-2 h-2 rounded-full',
                  tenant.is_active ? 'bg-green-500' : 'bg-gray-300'
                ]"
              ></div>
              <span>{{ tenant.name }}</span>
            </div>

            <svg
              v-if="currentTenant?.id === tenant.id"
              class="w-4 h-4"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from '../../composables/VoltPanel/useToast'
import { useRouting } from '../../composables/VoltPanel/useRouting'

const { route } = useRouting()
const toast = useToast()

const props = defineProps({
  initialTenant: Object
})

const isOpen = ref(false)
const tenants = ref([])
const currentTenant = ref(props.initialTenant || null)
const dropdownRef = ref(null)

onMounted(async () => {
  await loadTenants()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

async function loadTenants() {
  try {
    const response = await axios.get(route('voltpanel.api.tenants.index'))
    tenants.value = response.data.tenants
    currentTenant.value = response.data.current
  } catch (error) {
    console.error('Failed to load tenants:', error)
  }
}

async function switchTenant(tenantId) {
  try {
    const response = await axios.post(route('voltpanel.api.tenants.switch', { tenantId }))

    currentTenant.value = response.data.tenant
    isOpen.value = false
    toast.success('Switched to ' + response.data.tenant.name)

    // Reload the page to apply tenant context
    setTimeout(() => window.location.reload(), 500)
  } catch (error) {
    console.error('Failed to switch tenant:', error)
    toast.error('Failed to switch tenant: ' + (error.response?.data?.message || error.message))
  }
}
</script>
