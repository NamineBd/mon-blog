import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: null as string | null,
    isLoading: false,
    error: null as string | null
  }),

  getters: {
    isAuthenticated: (state): boolean => !!state.token,
    isAdmin: (state): boolean => !!state.user?.is_admin,
    currentUser: (state): any => state.user
  },

  actions: {
    loadFromStorage() {
      if (process.client) {
        const token = localStorage.getItem('token')
        const userRaw = localStorage.getItem('user')
        if (token && userRaw) {
          try {
            this.token = token
            this.user = JSON.parse(userRaw)
          } catch {
            this.token = null
            this.user = null
          }
        }
      }
    },

    saveToStorage() {
      if (process.client && this.token && this.user) {
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
      }
    },

    clearStorage() {
      if (process.client) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },

    async register(name: string, email: string, password: string, passwordConfirmation: string) {
      this.isLoading = true
      this.error = null
      try {
        const { $fetch: api } = useNuxtApp()
        const res: any = await api('/register', {
          method: 'POST',
          body: { name, email, password, password_confirmation: passwordConfirmation }
        })
        this.user = res.user
        this.token = res.token
        this.saveToStorage()
        return true
      } catch (err: any) {
        const errors = err.data?.errors
        this.error = errors
          ? Object.values(errors).flat()[0] as string
          : (err.data?.message || 'Erreur lors de l\'inscription')
        return false
      } finally {
        this.isLoading = false
      }
    },

    async login(email: string, password: string) {
      this.isLoading = true
      this.error = null
      try {
        const { $fetch: api } = useNuxtApp()
        const res: any = await api('/login', {
          method: 'POST',
          body: { email, password }
        })
        this.user = res.user
        this.token = res.token
        this.saveToStorage()
        return true
      } catch (err: any) {
        const errors = err.data?.errors
        this.error = errors
          ? Object.values(errors).flat()[0] as string
          : (err.data?.message || 'Email ou mot de passe incorrect')
        return false
      } finally {
        this.isLoading = false
      }
    },

    async logout() {
      try {
        const { $fetch: api } = useNuxtApp()
        await api('/logout', { method: 'POST' })
      } catch { /* ignore */ } finally {
        this.user = null
        this.token = null
        this.error = null
        this.clearStorage()
        this.isLoading = false
      }
    },

    setError(msg: string | null) {
      this.error = msg
    }
  }
})