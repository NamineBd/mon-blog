<template>
  <div>
    <h1 class="section-title">Créer un article</h1>
    <p class="section-subtitle">Partagez vos idées avec la communauté</p>

    <form @submit.prevent="submitArticle" class="max-w-4xl">
      <!-- Titre -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Le titre de votre article"
          class="input-field text-lg font-semibold"
        />
      </div>

      <!-- Extrait -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Résumé (optionnel)</label>
        <textarea
          v-model="form.excerpt"
          placeholder="Un court résumé de votre article"
          class="textarea-field h-20"
        ></textarea>
      </div>

      <!-- Image de couverture -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Image de couverture (optionnel)</label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-500 transition">
          <input
            ref="coverImageInput"
            type="file"
            accept="image/*"
            @change="handleCoverImageChange"
            class="hidden"
          />
          <button
            type="button"
            @click="$refs.coverImageInput?.click()"
            class="inline-block"
          >
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <p class="text-gray-600">Cliquez pour télécharger une image</p>
            <p v-if="form.cover_image" class="text-sm text-green-600 mt-2">
              ✓ Image sélectionnée: {{ form.cover_image.name }}
            </p>
          </button>
        </div>
      </div>

      <!-- Contenu -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
        <textarea
          v-model="form.content"
          required
          placeholder="Écrivez le contenu de votre article ici..."
          class="textarea-field h-96"
        ></textarea>
      </div>

      <!-- Status -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
        <div class="flex gap-6">
          <label class="flex items-center cursor-pointer">
            <input
              v-model="form.status"
              type="radio"
              value="draft"
              class="w-4 h-4 text-indigo-600"
            />
            <span class="ml-2 text-gray-700">Brouillon</span>
          </label>
          <label class="flex items-center cursor-pointer">
            <input
              v-model="form.status"
              type="radio"
              value="published"
              class="w-4 h-4 text-indigo-600"
            />
            <span class="ml-2 text-gray-700">Publier maintenant</span>
          </label>
        </div>
      </div>

      <!-- Erreur -->
      <div v-if="articleStore.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
        {{ articleStore.error }}
      </div>

      <!-- Boutons -->
      <div class="flex gap-4">
        <button
          type="submit"
          :disabled="articleStore.isLoading"
          class="btn-primary"
        >
          <span v-if="!articleStore.isLoading">Publier l'article</span>
          <span v-else class="flex items-center gap-2">
            <div class="loading-spinner border-white border-t-indigo-600"></div>
            Création en cours...
          </span>
        </button>
        <NuxtLink to="/articles" class="btn-secondary">
          Annuler
        </NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
const articleStore = useArticleStore()
const router = useRouter()
const { success, error } = useNotification()

const form = reactive({
  title: '',
  excerpt: '',
  content: '',
  status: 'draft' as 'draft' | 'published',
  cover_image: null as File | null
})

const handleCoverImageChange = (e: Event) => {
  const input = e.target as HTMLInputElement
  if (input.files?.[0]) {
    form.cover_image = input.files[0]
  }
}

const submitArticle = async () => {
  const result = await articleStore.createArticle(form)

  if (result) {
    success(`Article ${form.status === 'published' ? 'publié' : 'créé'} avec succès!`)
    router.push(`/articles/${result.id}`)
  } else {
    error(articleStore.error || 'Erreur lors de la création')
  }
}

onMounted(() => {
  const authStore = useAuthStore()
  authStore.loadFromStorage()

  if (!authStore.isAuthenticated) {
    router.push('/auth/login')
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
