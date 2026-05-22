<template>
  <NuxtLink :to="`/articles/${article.id}`" class="block">
    <div class="article-card group overflow-hidden">
      <!-- Image -->
      <div v-if="article.cover_image" class="relative h-48 overflow-hidden bg-gray-200">
        <img
          :src="getImageUrl(article.cover_image)"
          :alt="article.title"
          class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
        />
      </div>

      <div v-else class="flex h-48 items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500">
        <svg class="h-12 w-12 text-white/50" fill="currentColor" viewBox="0 0 20 20">
          <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
        </svg>
      </div>

      <!-- Contenu -->
      <div class="p-6">
        <!-- Status -->
        <div class="mb-3 flex gap-2">
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
        <h3 class="mb-2 line-clamp-2 text-xl font-bold text-gray-900 transition group-hover:text-indigo-600">
          {{ article.title }}
        </h3>

        <!-- Extrait -->
        <p v-if="article.excerpt" class="mb-4 line-clamp-2 text-sm text-gray-600">
          {{ article.excerpt }}
        </p>

        <!-- Meta -->
        <div class="mt-5 flex items-center gap-3 text-sm text-gray-600">
          <div
            v-if="article.user"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white"
            :title="article.user.name"
          >
            {{ getInitials(article.user.name) }}
          </div>

          <div class="min-w-0">
            <div class="truncate text-xs font-medium text-gray-900">
              {{ article.user?.name || 'Auteur inconnu' }}
            </div>

            <time
              :datetime="article.published_at || article.created_at"
              class="block text-xs text-gray-500"
            >
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
  if (!path) return ''
  const cleanPath = path.replace(/^\/?storage\//, '')
  return `${backendUrl}/storage/${cleanPath}`
}

const getInitials = (name: string) => {
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map(part => part[0]?.toUpperCase())
    .join('')
}
</script>