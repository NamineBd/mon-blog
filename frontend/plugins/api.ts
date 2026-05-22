export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase,

    onRequest({ options }) {
      const token = authStore.token ?? (process.client ? localStorage.getItem('token') : null)
      if (token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${token}`
        }
      }
    },

    async onResponseError({ response }) {
      if (response.status === 401) {
        authStore.token = null
        authStore.user = null
        if (process.client) {
          localStorage.removeItem('token')
          localStorage.removeItem('user')
        }
        await navigateTo('/auth/login')
      }
    }
  })

  return {
    provide: { apiFetch }
  }
})
