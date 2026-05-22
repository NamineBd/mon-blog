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
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const comments = await $fetch(`/articles/${articleId}/comments`)
        this.comments = comments
        return comments
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors du chargement des commentaires'
        return []
      } finally {
        this.isLoading = false
      }
    },

    async createComment(articleId: number, content: string) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const comment = await $fetch(`/articles/${articleId}/comments`, {
          method: 'POST',
          body: { content }
        })

        this.comments.unshift(comment)
        return comment
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la création du commentaire'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async updateComment(commentId: number, content: string) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const comment = await $fetch(`/comments/${commentId}`, {
          method: 'PUT',
          body: { content }
        })

        const index = this.comments.findIndex(c => c.id === commentId)
        if (index !== -1) {
          this.comments[index] = comment
        }

        return comment
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la modification'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async deleteComment(commentId: number) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        await $fetch(`/comments/${commentId}`, { method: 'DELETE' })

        this.comments = this.comments.filter(c => c.id !== commentId)
        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la suppression'
        return false
      } finally {
        this.isLoading = false
      }
    },

    clearComments() {
      this.comments = []
    }
  }
})