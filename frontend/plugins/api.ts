export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig()

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase as string,

    onRequest({ options }) {
      // Lire le token depuis localStorage côté client
      if (process.client) {
        const token = localStorage.getItem('token')
        if (token) {
          const headers = new Headers(options.headers as HeadersInit)
          headers.set('Authorization', `Bearer ${token}`)
          options.headers = headers
        }
      }
    },

    onResponseError({ response }) {
      if (response.status === 401 && process.client) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        navigateTo('/auth/login')
      }
    }
  })

  return {
    provide: {
      fetch: apiFetch
    }
  }
})