import { onMounted, onUnmounted } from 'vue'

export function useKeyboardShortcuts(shortcuts) {
  const handleKeyDown = (event) => {
    const key = getKeyCombo(event)

    if (shortcuts[key]) {
      event.preventDefault()
      shortcuts[key]()
    }
  }

  const getKeyCombo = (event) => {
    const keys = []

    if (event.ctrlKey || event.metaKey) keys.push('ctrl')
    if (event.shiftKey) keys.push('shift')
    if (event.altKey) keys.push('alt')

    keys.push(event.key.toLowerCase())

    return keys.join('+')
  }

  onMounted(() => {
    document.addEventListener('keydown', handleKeyDown)
  })

  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown)
  })

  return {
    handleKeyDown,
    getKeyCombo
  }
}
