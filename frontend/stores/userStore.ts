import { defineStore } from 'pinia'

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [] as any[],
    isLoading: false,
    error: null as string | null,
    pagination: { current_page: 1, total: 0, last_page: 1 }
  }),

  actions: {
    async fetchUsers(page = 1) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/users', { query: { page } })
        this.users = res.data
        this.pagination = { current_page: res.current_page, total: res.total, last_page: res.last_page }
        return res
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur chargement utilisateurs'
        return null
      } finally { this.isLoading = false }
    },

    async createUser(data: any) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/users', {
          method: 'POST',
          body: {
            name: data.name, email: data.email,
            password: data.password, password_confirmation: data.passwordConfirmation,
            is_admin: data.isAdmin || false
          }
        })
        this.users.unshift(res.user)
        return res.user
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur création utilisateur'
        return null
      } finally { this.isLoading = false }
    },

    async deleteUser(id: number) {
      try {
        const { $apiFetch } = useNuxtApp()
        await $apiFetch(`/users/${id}`, { method: 'DELETE' })
        this.users = this.users.filter(u => u.id !== id)
        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur suppression utilisateur'
        return false
      }
    }
  }
})
