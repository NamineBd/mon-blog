import { defineStore } from 'pinia'

export const useCommentStore = defineStore('comments', {
  state: () => ({
    comments: [] as any[],
    isLoading: false,
    error: null as string | null
  }),

  actions: {
    async fetchArticleComments(articleId: number) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const comments: any = await $apiFetch(`/articles/${articleId}/comments`)
        this.comments = comments
        return comments
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur chargement commentaires'
        return []
      } finally { this.isLoading = false }
    },

    async createComment(articleId: number, content: string) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const comment: any = await $apiFetch(`/articles/${articleId}/comments`, {
          method: 'POST', body: { content }
        })
        this.comments.unshift(comment)
        return comment
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur création commentaire'
        return null
      } finally { this.isLoading = false }
    },

    async updateComment(commentId: number, content: string) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const comment: any = await $apiFetch(`/comments/${commentId}`, {
          method: 'PUT', body: { content }
        })
        const idx = this.comments.findIndex(c => c.id === commentId)
        if (idx !== -1) this.comments[idx] = comment
        return comment
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur modification commentaire'
        return null
      } finally { this.isLoading = false }
    },

    async deleteComment(commentId: number) {
      try {
        const { $apiFetch } = useNuxtApp()
        await $apiFetch(`/comments/${commentId}`, { method: 'DELETE' })
        this.comments = this.comments.filter(c => c.id !== commentId)
        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur suppression commentaire'
        return false
      }
    }
  }
})
