<template>
  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="section-title">Tous les articles</h1>
      <p class="section-subtitle">Explorez notre collection d'articles publiés</p>

      <div class="flex gap-4 flex-wrap">
        <NuxtLink to="/articles/create" class="btn-primary">
          + Créer un article
        </NuxtLink>
        <NuxtLink to="/articles/mine" class="btn-outline">
          Mes articles
        </NuxtLink>
      </div>
    </div>

    <!-- Filtres -->
    <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
      <div class="flex gap-4 flex-wrap items-center">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher des articles..."
          class="flex-1 min-w-64 input-field"
        />
        <select v-model="statusFilter" class="input-field">
          <option value="">Tous les statuts</option>
          <option value="published">Publiés</option>
          <option value="draft">Brouillons</option>
        </select>
      </div>
    </div>

    <!-- Articles -->
    <div v-if="articleStore.isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="card h-80 animate-pulse bg-gray-200"></div>
    </div>

    <div v-else-if="filteredArticles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ArticleCard
        v-for="article in filteredArticles"
        :key="article.id"
        :article="article"
      />
    </div>

    <div v-else class="card text-center py-12">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17.001c0 5.591 3.824 10.29 9 11.622m0-13c5.5 0 10-4.747 10-10.999C22 5.254 17.5.5 12 .5m0 13v13m0-13C6.5 30.253 2 25.498 2 19.001c0-5.591 3.824-10.29 9-11.622" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun article trouvé</h3>
      <p class="text-gray-600 mb-6">
        {{ searchQuery ? 'Essayez une autre recherche' : 'Commencez par créer un article' }}
      </p>
      <NuxtLink to="/articles/create" class="btn-primary">
        Créer mon premier article
      </NuxtLink>
    </div>

    <!-- Pagination -->
    <div v-if="articleStore.pagination.last_page > 1" class="mt-12 flex justify-center gap-2">
      <button
        v-for="page in range(1, articleStore.pagination.last_page + 1)"
        :key="page"
        @click="goToPage(page)"
        :class="[
          'px-4 py-2 rounded-lg font-semibold transition',
          articleStore.pagination.current_page === page
            ? 'bg-indigo-600 text-white'
            : 'bg-white text-gray-700 border border-gray-200 hover:border-indigo-600'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
const articleStore = useArticleStore()
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)

const filteredArticles = computed(() => {
  return articleStore.articles.filter(article => {
    const matchesSearch = article.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || article.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const goToPage = async (page: number) => {
  currentPage.value = page
  await articleStore.fetchArticles(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const range = (start: number, end: number) => {
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
}

onMounted(async () => {
  const authStore = useAuthStore()
  const router = useRouter()

  authStore.loadFromStorage()
  if (!authStore.isAuthenticated) {
    router.push('/auth/login')
    return
  }

  await articleStore.fetchArticles()
})

definePageMeta({
  middleware: 'auth'
})
</script>
