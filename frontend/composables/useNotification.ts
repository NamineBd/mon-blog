// État global partagé entre tous les composants
const notifications = ref<{ id: number; message: string; type: 'success' | 'error' | 'info' }[]>([])

export const useNotification = () => {
  const add = (message: string, type: 'success' | 'error' | 'info' = 'info', duration = 3500) => {
    const id = Date.now()
    notifications.value.push({ id, message, type })
    setTimeout(() => {
      notifications.value = notifications.value.filter(n => n.id !== id)
    }, duration)
  }

  return {
    notifications: readonly(notifications),
    success: (msg: string) => add(msg, 'success'),
    error: (msg: string) => add(msg, 'error'),
    info: (msg: string) => add(msg, 'info')
  }
}
