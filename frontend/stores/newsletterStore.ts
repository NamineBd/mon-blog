import { defineStore } from 'pinia'

export const useNewsletterStore = defineStore('newsletter', {
  state: () => ({
    subscribers: [] as any[],
    isLoading: false,
    error: null as string | null,
    pagination: { current_page: 1, total: 0, last_page: 1 }
  }),

  actions: {
    async subscribe(email: string) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/newsletter/subscribe', {
          method: 'POST', body: { email }
        })
        return { success: true, message: res.message }
      } catch (err: any) {
        const msg = err.data?.message || 'Erreur lors de l\'abonnement'
        return { success: false, message: msg }
      } finally { this.isLoading = false }
    },

    async fetchSubscribers(page = 1) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/admin/subscribers', { query: { page } })
        this.subscribers = res.data
        this.pagination = { current_page: res.current_page, total: res.total, last_page: res.last_page }
        return res
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur chargement abonnés'
        return null
      } finally { this.isLoading = false }
    }
  }
})
