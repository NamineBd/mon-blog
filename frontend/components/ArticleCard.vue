<template>
  <NuxtLink :to="`/articles/${article.id}`">
    <div class="article-card group">
      <!-- Image -->
      <div v-if="article.cover_image" class="relative overflow-hidden h-48 bg-gray-200">
        <img
          :src="getImageUrl(article.cover_image)"
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
        <!-- Meta : avatar à gauche, nom + date en colonne à droite -->
        <div class="flex items-center gap-3 text-sm mt-5 text-gray-600">
          <img
            v-if="article.user"
            :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(article.user.name)}&background=6366f1&color=fff&size=6`"
            :alt="article.user.name"
            class="w-7 h-7 rounded-full"
          />
          <div class="flex items-center gap-2 text-xs">
            <span class="font-medium text-gray-900">{{ article.user?.name }}</span>
            <span class="text-gray-400">•</span>
            <time :datetime="article.published_at || article.created_at" class="text-gray-500 text-xs mt-0.5">
              {{ formatDate(article.published_at || article.created_at) }}
            </time>
          </div>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const apiBase = config.public.apiBase
const backendUrl = config.public.backendUrl

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

const getImageUrl = (path: string | null | undefined) => {
  if (!path) return null
  // Supprime un éventuel 'storage/' en trop
  const cleanPath = path.replace(/^\/?storage\//, '')
  return `${backendUrl}/storage/${cleanPath}`
}
</script>
