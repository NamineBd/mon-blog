export const useNotification = () => {
  const notifications = ref<any[]>([])

  const add = (message: string, type: 'success' | 'error' | 'info' = 'info', duration: number = 3000) => {
    const id = Date.now()
    const notification = { id, message, type }
    notifications.value.push(notification)

    setTimeout(() => {
      notifications.value = notifications.value.filter(n => n.id !== id)
    }, duration)

    return id
  }

  const success = (message: string) => add(message, 'success')
  const error = (message: string) => add(message, 'error')
  const info = (message: string) => add(message, 'info')

  return {
    notifications,
    add,
    success,
    error,
    info
  }
}