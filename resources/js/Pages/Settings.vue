<template>
  <PanelLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h2>
          <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ description }}
          </p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <form @submit.prevent="save" class="space-y-6 p-6">
          <!-- Settings grouped by section -->
          <div v-for="(group, groupName) in groupedSettings" :key="groupName" class="space-y-4">
            <h3 v-if="groupName !== 'general'" class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              {{ formatGroupName(groupName) }}
            </h3>

            <div v-for="setting in group" :key="setting.key" class="space-y-2">
              <FormField :component="mapSettingToComponent(setting)" v-model="form[setting.key]" />
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              type="button"
              @click="reset"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              Reset
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 text-sm font-medium bg-primary text-primary-foreground rounded-lg hover:opacity-90 disabled:opacity-50 transition-all"
            >
              {{ processing ? 'Saving...' : 'Save Settings' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '../../layouts/VoltPanel/PanelLayout.vue'
import FormField from '../../components/VoltPanel/FormField.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Settings'
  },
  description: String,
  settings: Array,
  values: Object,
})

const form = reactive({})
const processing = ref(false)

// Initialize form with current values
props.settings.forEach(setting => {
  form[setting.key] = props.values?.[setting.key] ?? setting.default
})

const groupedSettings = computed(() => {
  const groups = {}

  props.settings.forEach(setting => {
    const group = setting.group || 'general'
    if (!groups[group]) {
      groups[group] = []
    }
    groups[group].push(setting)
  })

  return groups
})

function formatGroupName(name) {
  return name
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

function save() {
  processing.value = true

  router.post(window.location.href, form, {
    onSuccess: () => {
      processing.value = false
    },
    onError: () => {
      processing.value = false
    }
  })
}

function reset() {
  props.settings.forEach(setting => {
    form[setting.key] = setting.default
  })
}

function mapSettingToComponent(setting) {
  // Map setting type to FormField component type
  const typeMap = {
    'text': 'text-input',
    'email': 'text-input',
    'url': 'text-input',
    'tel': 'text-input',
    'textarea': 'textarea',
    'select': 'select',
    'toggle': 'toggle',
    'boolean': 'toggle',
  }

  return {
    name: setting.key,
    label: setting.label,
    helperText: setting.description,
    type: typeMap[setting.type] || setting.type,
    inputType: ['email', 'url', 'tel'].includes(setting.type) ? setting.type : 'text',
    required: false,
  }
}
</script>
