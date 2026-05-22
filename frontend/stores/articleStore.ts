import { defineStore } from 'pinia'

export const useArticleStore = defineStore('articles', {
  state: () => ({
    articles: [] as any[],
    currentArticle: null as any,
    isLoading: false,
    error: null as string | null,
    pagination: { current_page: 1, total: 0, last_page: 1 }
  }),

  actions: {
    async fetchArticles(page = 1) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        const res: any = await $apiFetch('/articles', { query: { page } })
        this.articles = res.data
        this.pagination = { current_page: res.current_page, total: res.total, last_page: res.last_page }
        return res
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur chargement articles'
        return null
      } finally { this.isLoading = false }
    },

    async fetchArticleById(id: number) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        const article: any = await $apiFetch(`/articles/${id}`)
        this.currentArticle = article
        return article
      } catch (err: any) {
        this.error = err.data?.message || 'Article non trouvé'
        return null
      } finally { this.isLoading = false }
    },

    async createArticle(data: any) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        const fd = new FormData()
        fd.append('title', data.title)
        fd.append('content', data.content)
        fd.append('status', data.status)
        if (data.excerpt) fd.append('excerpt', data.excerpt)
        if (data.cover_image) fd.append('cover_image', data.cover_image)
        const article: any = await $apiFetch('/articles', { method: 'POST', body: fd })
        this.articles.unshift(article)
        return article
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur création article'
        return null
      } finally { this.isLoading = false }
    },

    async updateArticle(id: number, data: any) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        const fd = new FormData()
        if (data.title) fd.append('title', data.title)
        if (data.content) fd.append('content', data.content)
        if (data.excerpt) fd.append('excerpt', data.excerpt)
        if (data.status) fd.append('status', data.status)
        if (data.cover_image) fd.append('cover_image', data.cover_image)
        const article: any = await $apiFetch(`/articles/${id}`, { method: 'POST', body: fd, query: { _method: 'PATCH' } })
        const idx = this.articles.findIndex(a => a.id === id)
        if (idx !== -1) this.articles[idx] = article
        if (this.currentArticle?.id === id) this.currentArticle = article
        return article
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur modification article'
        return null
      } finally { this.isLoading = false }
    },

    async deleteArticle(id: number) {
      this.isLoading = true
      this.error = null
      try {
        const { $apiFetch } = useNuxtApp()
        await $apiFetch(`/article-images/${id}`, { method: 'DELETE' })
        this.articles = this.articles.filter(a => a.id !== id)
        if (this.currentArticle?.id === id) this.currentArticle = null
        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur suppression article'
        return false
      } finally { this.isLoading = false }
    },

    async uploadArticleImage(articleId: number, file: File) {
      this.isLoading = true
      try {
        const { $apiFetch } = useNuxtApp()
        const fd = new FormData()
        fd.append('image', file)
        const image: any = await $apiFetch(`/articles/${articleId}/images`, { method: 'POST', body: fd })
        if (this.currentArticle?.id === articleId) {
          if (!this.currentArticle.images) this.currentArticle.images = []
          this.currentArticle.images.push(image)
        }
        return image
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur upload image'
        return null
      } finally { this.isLoading = false }
    },

    async deleteArticleImage(imageId: number) {
      try {
        const { $apiFetch } = useNuxtApp()
        await $apiFetch(`/article-images/${imageId}`, { method: 'DELETE' })
        if (this.currentArticle?.images) {
          this.currentArticle.images = this.currentArticle.images.filter((img: any) => img.id !== imageId)
        }
        return true
      } catch (err: any) {
        this.error = err.data?.message || 'Erreur suppression image'
        return false
      }
    }
  }
})
