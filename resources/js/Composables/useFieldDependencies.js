import { ref, computed, watch } from 'vue'

export function useFieldDependencies(field, formData) {
  const isVisible = ref(true)
  const isRequired = ref(field.required || false)
  const isDisabled = ref(field.disabled || false)

  // Evaluate a single condition
  const evaluateCondition = (fieldValue, compareValue, operator) => {
    switch (operator) {
      case '=':
      case '==':
        return fieldValue == compareValue
      case '!=':
      case '!==':
        return fieldValue != compareValue
      case '>':
        return fieldValue > compareValue
      case '>=':
        return fieldValue >= compareValue
      case '<':
        return fieldValue < compareValue
      case '<=':
        return fieldValue <= compareValue
      case 'in':
        return Array.isArray(compareValue) && compareValue.includes(fieldValue)
      case 'not_in':
        return Array.isArray(compareValue) && !compareValue.includes(fieldValue)
      case 'contains':
        return String(fieldValue).includes(String(compareValue))
      case 'starts_with':
        return String(fieldValue).startsWith(String(compareValue))
      case 'ends_with':
        return String(fieldValue).endsWith(String(compareValue))
      case 'filled':
        return fieldValue !== null && fieldValue !== undefined && fieldValue !== ''
      case 'blank':
        return fieldValue === null || fieldValue === undefined || fieldValue === ''
      default:
        return false
    }
  }

  // Evaluate dependencies
  const evaluateDependencies = () => {
    // Check visibleWhen
    if (field.visibleWhen) {
      const deps = field.visibleWhen.dependencies || []
      isVisible.value = deps.every(dep => {
        const fieldValue = formData[dep.field]
        return evaluateCondition(fieldValue, dep.value, dep.operator)
      })
    }

    // Check hiddenWhen
    if (field.hiddenWhen) {
      const deps = field.hiddenWhen.dependencies || []
      const shouldHide = deps.every(dep => {
        const fieldValue = formData[dep.field]
        return evaluateCondition(fieldValue, dep.value, dep.operator)
      })
      if (shouldHide) {
        isVisible.value = false
      }
    }

    // Check requiredWhen
    if (field.requiredWhen) {
      const deps = field.requiredWhen.dependencies || []
      isRequired.value = deps.every(dep => {
        const fieldValue = formData[dep.field]
        return evaluateCondition(fieldValue, dep.value, dep.operator)
      })
    }

    // Check disabledWhen
    if (field.disabledWhen) {
      const deps = field.disabledWhen.dependencies || []
      isDisabled.value = deps.every(dep => {
        const fieldValue = formData[dep.field]
        return evaluateCondition(fieldValue, dep.value, dep.operator)
      })
    }
  }

  // Watch for changes in dependent fields
  if (field.dependencies && field.dependencies.length > 0) {
    field.dependencies.forEach(dep => {
      watch(
        () => formData[dep.field],
        () => {
          evaluateDependencies()
        },
        { immediate: true }
      )
    })
  } else {
    // Evaluate once if no dependencies
    evaluateDependencies()
  }

  return {
    isVisible,
    isRequired,
    isDisabled
  }
}
