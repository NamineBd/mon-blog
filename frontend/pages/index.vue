<template>
  <div>
    <!-- Hero Section -->
    <section class="mb-16 animate-fade-in-up">
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl px-8 py-16 text-white">
        <h1 class="text-5xl font-bold mb-4">Bienvenue sur BlogHub</h1>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl">
          Découvrez des articles captivants, partagez vos idées et rejoignez notre communauté de blogueurs passionnés.
        </p>
        <div class="flex gap-4 flex-wrap">
          <NuxtLink to="/articles" class="btn bg-white text-indigo-600 font-bold hover:bg-gray-100">
            Lire les articles
          </NuxtLink>
          <a href="#newsletter" class="btn border-2 border-white text-white hover:bg-white hover:text-indigo-600">
            S'abonner à la newsletter
          </a>
        </div>
      </div>
    </section>

    <!-- Articles récents -->
    <section class="mb-16">
      <div class="flex items-center justify-between mb-8">
        <h2 class="section-title">Articles récents</h2>
        <NuxtLink to="/articles" class="text-indigo-600 font-semibold hover:underline">
          Voir tous →
        </NuxtLink>
      </div>

      <div v-if="articleStore.isLoading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="h-64 rounded-xl animate-pulse bg-gray-200"></div>
      </div>

      <div v-else-if="articleStore.articles.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <ArticleCard
          v-for="article in articleStore.articles.slice(0, 3)"
          :key="article.id"
          :article="article"
        />
      </div>

      <div v-else class="card text-center py-12">
        <p class="text-gray-500 mb-4">Aucun article pour le moment. Commencez à écrire!</p>
        <NuxtLink to="/articles/create" class="btn-primary">Créer le premier article</NuxtLink>
      </div>
    </section>

    <!-- Newsletter -->
    <section id="newsletter" class="mb-16">
      <div class="bg-indigo-50 rounded-2xl px-8 py-12 border border-indigo-200">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="section-title">Restez informé</h2>
          <p class="text-gray-600 mb-8">
            Abonnez-vous à notre newsletter pour recevoir les derniers articles directement dans votre boîte mail.
          </p>
          <NewsletterForm />
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="card text-center">
        <div class="text-4xl font-bold text-indigo-600 mb-2">{{ articleStore.pagination.total || '—' }}</div>
        <p class="text-gray-600">Articles publiés</p>
      </div>
      <div class="card text-center">
        <div class="text-4xl font-bold text-indigo-600 mb-2">∞</div>
        <p class="text-gray-600">Idées à partager</p>
      </div>
      <div class="card text-center">
        <div class="text-4xl font-bold text-indigo-600 mb-2">{{ newsletterStore.pagination.total || '—' }}</div>
        <p class="text-gray-600">Abonnés newsletter</p>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const articleStore = useArticleStore()
const newsletterStore = useNewsletterStore()

// Le middleware global gère déjà la protection de la route.
// On charge les données directement.
onMounted(async () => {
  await articleStore.fetchArticles()
})
</script>
