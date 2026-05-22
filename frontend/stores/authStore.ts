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
    currentUser: (state) => state.user
  },

  actions: {
    loadFromStorage() {
      if (!process.client) return
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      if (token && user) {
        try {
          this.token = token
          this.user = JSON.parse(user)
        } catch {
          this.token = null
          this.user = null
        }
      }
    },

    _save() {
      if (!process.client) return
      localStorage.setItem('token', this.token!)
      localStorage.setItem('user', JSON.stringify(this.user))
    },

    _clear() {
      if (!process.client) return
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    },

    async register(name: string, email: string, password: string, passwordConfirmation: string) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/register', {
          method: 'POST',
          body: { name, email, password, password_confirmation: passwordConfirmation }
        })
        this.user = res.user
        this.token = res.token
        this._save()
        return true
      } catch (err: any) {
        const errors = err.data?.errors
        if (errors) {
          this.error = Object.values(errors).flat()[0] as string
        } else {
          this.error = err.data?.message || 'Erreur lors de l\'inscription'
        }
        return false
      } finally {
        this.isLoading = false
      }
    },

    async login(email: string, password: string) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/login', {
          method: 'POST',
          body: { email, password }
        })
        this.user = res.user
        this.token = res.token
        this._save()
        return true
      } catch (err: any) {
        const errors = err.data?.errors
        if (errors) {
          this.error = Object.values(errors).flat()[0] as string
        } else {
          this.error = err.data?.message || 'Email ou mot de passe incorrect'
        }
        return false
      } finally {
        this.isLoading = false
      }
    },

    async logout() {
      try {
        const { $apiFetch } = useNuxtApp()
        await $apiFetch('/logout', { method: 'POST' })
      } catch { /* ignore */ }
      this.user = null
      this.token = null
      this.error = null
      this._clear()
    },

    setError(msg: string | null) {
      this.error = msg
    }
  }
})
