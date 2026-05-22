export const useApi = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const request = async (endpoint: string, options: any = {}) => {
    const headers: any = {
      'Content-Type': 'application/json',
      ...options.headers
    }

    // Ajouter le token si disponible
    if (authStore.token) {
      headers['Authorization'] = `Bearer ${authStore.token}`
    }

    const defaultOptions = {
      baseURL: config.public.apiBase,
      headers,
      ...options
    }

    try {
      const response = await $fetch(endpoint, defaultOptions)
      return response
    } catch (error: any) {
      // Si erreur 401, déconnecter l'utilisateur
      if (error.status === 401) {
        authStore.logout()
        navigateTo('/auth/login')
      }
      throw error
    }
  }

  return {
    request,
    get: (endpoint: string, options: any = {}) => 
      request(endpoint, { ...options, method: 'GET' }),
    
    post: (endpoint: string, body: any, options: any = {}) => 
      request(endpoint, { ...options, method: 'POST', body }),
    
    put: (endpoint: string, body: any, options: any = {}) => 
      request(endpoint, { ...options, method: 'PUT', body }),
    
    patch: (endpoint: string, body: any, options: any = {}) => 
      request(endpoint, { ...options, method: 'PATCH', body }),
    
    delete: (endpoint: string, options: any = {}) => 
      request(endpoint, { ...options, method: 'DELETE' })
  }
}
