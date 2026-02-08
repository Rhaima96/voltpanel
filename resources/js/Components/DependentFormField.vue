<template>
  <div v-if="isVisible" :class="{ 'opacity-50 pointer-events-none': isDisabled }">
    <FormField
      :field="computedField"
      :model-value="modelValue"
      @update:model-value="$emit('update:modelValue', $event)"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import FormField from './FormField.vue'
import { useFieldDependencies } from '../../composables/VoltPanel/useFieldDependencies'

const props = defineProps({
  field: {
    type: Object,
    required: true
  },
  modelValue: {
    required: false
  },
  formData: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])

const { isVisible, isRequired, isDisabled } = useFieldDependencies(props.field, props.formData)

const computedField = computed(() => ({
  ...props.field,
  required: isRequired.value,
  disabled: isDisabled.value
}))
</script>
