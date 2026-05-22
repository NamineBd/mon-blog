import { defineStore } from 'pinia'

export const useNewsletterStore = defineStore('newsletter', {
  state: () => ({
    subscribers: [] as any[],
    isLoading: false,
    error: null as string | null,
    pagination: {
      current_page: 1,
      total: 0,
      last_page: 1
    }
  }),

  actions: {
    async subscribe(email: string) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/newsletter/subscribe', {
          method: 'POST',
          body: { email }
        })

        return { success: true, message: response.message }
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de l\'abonnement'
        return { success: false, message: this.error }
      } finally {
        this.isLoading = false
      }
    },

    async fetchSubscribers(page: number = 1) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/admin/subscribers', {
          query: { page }
        })

        this.subscribers = response.data
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
    }
  }
})