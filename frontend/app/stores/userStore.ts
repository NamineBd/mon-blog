import { defineStore } from 'pinia'

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [] as any[],
    isLoading: false,
    error: null as string | null,
    pagination: {
      current_page: 1,
      total: 0,
      last_page: 1
    }
  }),

  actions: {
    async fetchUsers(page: number = 1) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/users', {
          query: { page }
        })

        this.users = response.data
        this.pagination = {
          current_page: response.current_page,
          total: response.total,
          last_page: response.last_page
        }

        return response
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors du chargement'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async createUser(data: any) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/users', {
          method: 'POST',
          body: {
            name: data.name,
            email: data.email,
            password: data.password,
            password_confirmation: data.passwordConfirmation,
            is_admin: data.isAdmin || false
          }
        })

        this.users.unshift(response.user)
        return response.user
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la création'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async updateUser(id: number, data: any) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch(`/users/${id}`, {
          method: 'PUT',
          body: data
        })

        const index = this.users.findIndex(u => u.id === id)
        if (index !== -1) {
          this.users[index] = response.user
        }

        return response.user
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la modification'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async deleteUser(id: number) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        await $fetch(`/users/${id}`, { method: 'DELETE' })

        this.users = this.users.filter(u => u.id !== id)
        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la suppression'
        return false
      } finally {
        this.isLoading = false
      }
    }
  }
})