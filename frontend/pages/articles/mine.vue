<template>
  <div>
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="section-title">Mes articles</h1>
        <p class="section-subtitle">Gérez vos articles publiés et brouillons</p>
      </div>
      <NuxtLink to="/articles/create" class="btn-primary">
        + Nouvel article
      </NuxtLink>
    </div>

    <!-- Onglets -->
    <div class="flex gap-4 mb-8 border-b border-gray-200">
      <button
        v-for="tab in ['all', 'published', 'draft']"
        :key="tab"
        @click="activeTab = tab"
        :class="[
          'px-4 py-3 font-semibold border-b-2 transition',
          activeTab === tab
            ? 'text-indigo-600 border-indigo-600'
            : 'text-gray-600 border-transparent hover:text-gray-900'
        ]"
      >
        {{
          tab === 'all' ? 'Tous' : tab === 'published' ? 'Publiés' : 'Brouillons'
        }}
        ({{ getCount(tab) }})
      </button>
    </div>

    <!-- Articles -->
    <div v-if="articleStore.isLoading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="h-24 card animate-pulse bg-gray-200"></div>
    </div>

    <div v-else-if="filteredArticles.length > 0" class="space-y-4">
      <div
        v-for="article in filteredArticles"
        :key="article.id"
        class="card hover:shadow-lg transition"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-bold text-gray-900">{{ article.title }}</h3>
              <span
                :class="[
                  'badge',
                  article.status === 'published' ? 'badge-success' : 'badge-warning'
                ]"
              >
                {{ article.status === 'published' ? 'Publié' : 'Brouillon' }}
              </span>
            </div>
            <p v-if="article.excerpt" class="text-gray-600 mb-2 line-clamp-2">
              {{ article.excerpt }}
            </p>
            <div class="text-sm text-gray-500">
              <time :datetime="article.published_at || article.created_at">
                {{ formatDate(article.published_at || article.created_at) }}
              </time>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 ml-4">
            <NuxtLink
              :to="`/articles/${article.id}`"
              class="btn-outline btn-sm"
            >
              Voir
            </NuxtLink>
            <NuxtLink
              :to="`/articles/${article.id}/edit`"
              class="btn-outline btn-sm"
            >
              Modifier
            </NuxtLink>
            <button
              @click="deleteArticleConfirm(article.id)"
              class="btn-danger btn-sm"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500 mb-4">Aucun article dans cette catégorie</p>
      <NuxtLink to="/articles/create" class="btn-primary">
        Créer mon premier article
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
const articleStore = useArticleStore()
const router = useRouter()
const { success, error } = useNotification()
const activeTab = ref('all')

const filteredArticles = computed(() => {
  if (activeTab.value === 'all') return articleStore.articles
  return articleStore.articles.filter(a => a.status === activeTab.value)
})

const getCount = (tab: string) => {
  if (tab === 'all') return articleStore.articles.length
  return articleStore.articles.filter(a => a.status === tab).length
}

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(new Date(date))
}

const deleteArticleConfirm = async (id: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet article?')) {
    const result = await articleStore.deleteArticle(id)
    if (result) {
      success('Article supprimé!')
    } else {
      error(articleStore.error || 'Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  const authStore = useAuthStore()
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