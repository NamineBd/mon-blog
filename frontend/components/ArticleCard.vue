<template>
  <NuxtLink :to="`/articles/${article.id}`">
    <div class="article-card group">
      <!-- Image -->
      <div v-if="article.cover_image" class="relative overflow-hidden h-48 bg-gray-200">
        <img
          :src="`${apiBase}/storage/${article.cover_image}`"
          :alt="article.title"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
      </div>
      <div v-else class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
        <svg class="w-12 h-12 text-white opacity-50" fill="currentColor" viewBox="0 0 20 20">
          <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
        </svg>
      </div>

      <!-- Contenu -->
      <div class="p-6">
        <!-- Status -->
        <div class="flex gap-2 mb-3">
          <span
            :class="[
              'badge',
              article.status === 'published' ? 'badge-success' : 'badge-warning'
            ]"
          >
            {{ article.status === 'published' ? 'Publié' : 'Brouillon' }}
          </span>
        </div>

        <!-- Titre -->
        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition">
          {{ article.title }}
        </h3>

        <!-- Extrait -->
        <p v-if="article.excerpt" class="text-gray-600 text-sm mb-4 line-clamp-2">
          {{ article.excerpt }}
        </p>

        <!-- Meta -->
        <div class="flex items-center justify-between text-sm text-gray-500">
          <div class="flex items-center gap-2">
            <img
              v-if="article.user"
              :src="`https://ui-avatars.com/api/?name=${article.user.name}`"
              :alt="article.user.name"
              class="w-6 h-6 rounded-full"
            />
            <span>{{ article.user?.name }}</span>
          </div>
          <time :datetime="article.published_at || article.created_at">
            {{ formatDate(article.published_at || article.created_at) }}
          </time>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

interface Article {
  id: number
  title: string
  excerpt?: string
  cover_image?: string
  status: 'draft' | 'published'
  published_at?: string
  created_at: string
  user?: {
    name: string
    email: string
  }
}

defineProps<{
  article: Article
}>()

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(new Date(date))
}
</script>