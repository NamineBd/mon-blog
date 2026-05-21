import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: null as string | null,
    isLoading: false,
    error: null as string | null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.is_admin || false,
    currentUser: (state) => state.user
  },

  actions: {
    async register(name: string, email: string, password: string, passwordConfirmation: string) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/register', {
          method: 'POST',
          body: {
            name,
            email,
            password,
            password_confirmation: passwordConfirmation
          }
        })

        this.user = response.user
        this.token = response.token
        
        // Sauvegarde dans localStorage
        if (process.client) {
          localStorage.setItem('token', response.token)
          localStorage.setItem('user', JSON.stringify(response.user))
        }

        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de l\'inscription'
        return false
      } finally {
        this.isLoading = false
      }
    },

    async login(email: string, password: string) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/login', {
          method: 'POST',
          body: { email, password }
        })

        this.user = response.user
        this.token = response.token

        if (process.client) {
          localStorage.setItem('token', response.token)
          localStorage.setItem('user', JSON.stringify(response.user))
        }

        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Email ou mot de passe incorrect'
        return false
      } finally {
        this.isLoading = false
      }
    },

    async logout() {
      this.isLoading = true
      try {
        const { $fetch } = useNuxtApp()
        await $fetch('/logout', { method: 'POST' })
      } catch (err) {
        console.error('Erreur lors de la déconnexion:', err)
      } finally {
        this.user = null
        this.token = null
        this.error = null
        this.isLoading = false

        if (process.client) {
          localStorage.removeItem('token')
          localStorage.removeItem('user')
        }
      }
    },

    async fetchCurrentUser() {
      try {
        const { $fetch } = useNuxtApp()
        const user = await $fetch('/me')
        this.user = user
        return user
      } catch (err) {
        console.error('Erreur lors de la récupération de l\'utilisateur:', err)
        this.logout()
        return null
      }
    },

    loadFromStorage() {
      if (process.client) {
        const token = localStorage.getItem('token')
        const user = localStorage.getItem('user')

        if (token && user) {
          this.token = token
          this.user = JSON.parse(user)
        }
      }
    },

    setError(error: string | null) {
      this.error = error
    }
  }
})