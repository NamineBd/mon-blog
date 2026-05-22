import { defineStore } from 'pinia'

export const useArticleStore = defineStore('articles', {
  state: () => ({
    articles: [] as any[],
    currentArticle: null as any,
    isLoading: false,
    error: null as string | null,
    pagination: {
      current_page: 1,
      total: 0,
      last_page: 1
    }
  }),

  actions: {
    async fetchArticles(page: number = 1) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const response = await $fetch('/articles', {
          query: { page }
        })

        this.articles = response.data
        this.pagination = {
          current_page: response.current_page,
          total: response.total,
          last_page: response.last_page
        }

        return response
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors du chargement des articles'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async fetchArticleById(id: number) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const article = await $fetch(`/articles/${id}`)
        this.currentArticle = article
        return article
      } catch (err: any) {
        this.error = err.data?.message || 'Article non trouvé'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async createArticle(data: any) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const formData = new FormData()
        
        formData.append('title', data.title)
        formData.append('content', data.content)
        if (data.excerpt) formData.append('excerpt', data.excerpt)
        if (data.cover_image) formData.append('cover_image', data.cover_image)
        formData.append('status', data.status)

        const article = await $fetch('/articles', {
          method: 'POST',
          body: formData
        })

        this.articles.unshift(article)
        return article
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la création'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async updateArticle(id: number, data: any) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const formData = new FormData()

        if (data.title) formData.append('title', data.title)
        if (data.content) formData.append('content', data.content)
        if (data.excerpt) formData.append('excerpt', data.excerpt)
        if (data.cover_image) formData.append('cover_image', data.cover_image)
        if (data.status) formData.append('status', data.status)

        const article = await $fetch(`/articles/${id}`, {
          method: 'PATCH',
          body: formData
        })

        const index = this.articles.findIndex(a => a.id === id)
        if (index !== -1) this.articles[index] = article

        if (this.currentArticle?.id === id) {
          this.currentArticle = article
        }

        return article
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la modification'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async deleteArticle(id: number) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        await $fetch(`/articles/${id}`, { method: 'DELETE' })

        this.articles = this.articles.filter(a => a.id !== id)
        
        if (this.currentArticle?.id === id) {
          this.currentArticle = null
        }

        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de la suppression'
        return false
      } finally {
        this.isLoading = false
      }
    },

    async uploadArticleImage(articleId: number, file: File) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        const formData = new FormData()
        formData.append('image', file)

        const image = await $fetch(`/articles/${articleId}/images`, {
          method: 'POST',
          body: formData
        })

        if (this.currentArticle?.id === articleId) {
          if (!this.currentArticle.images) {
            this.currentArticle.images = []
          }
          this.currentArticle.images.push(image)
        }

        return image
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur lors de l\'upload'
        return null
      } finally {
        this.isLoading = false
      }
    },

    async deleteArticleImage(imageId: number) {
      this.isLoading = true
      this.error = null

      try {
        const { $fetch } = useNuxtApp()
        await $fetch(`/article-images/${imageId}`, { method: 'DELETE' })

        if (this.currentArticle?.images) {
          this.currentArticle.images = this.currentArticle.images.filter((img: any) => img.id !== imageId)
        }

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