<template>
  <div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <NuxtLink :to="`/articles/${route.params.id}`" class="text-indigo-600 hover:underline text-sm mb-2 inline-block">
          ← Retour à l'article
        </NuxtLink>
        <h1 class="section-title">Modifier l'article</h1>
      </div>
    </div>

    <!-- Loader initial -->
    <div v-if="loadingArticle" class="flex items-center justify-center py-24">
      <div class="text-center">
        <div class="inline-block w-10 h-10 border-4 border-gray-200 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
        <p class="text-gray-500">Chargement de l'article...</p>
      </div>
    </div>

    <!-- Erreur chargement -->
    <div v-else-if="loadError" class="card text-center py-12">
      <p class="text-red-600 font-semibold mb-4">{{ loadError }}</p>
      <NuxtLink to="/articles" class="btn-primary">Retour aux articles</NuxtLink>
    </div>

    <!-- Formulaire -->
    <form v-else @submit.prevent="submitArticle" class="space-y-6">

      <!-- Titre -->
      <div class="card">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Titre <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Le titre de votre article"
          class="input-field text-lg font-semibold"
        />
      </div>

      <!-- Résumé -->
      <div class="card">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Résumé <span class="text-gray-400 font-normal">(optionnel)</span>
        </label>
        <textarea
          v-model="form.excerpt"
          rows="3"
          placeholder="Un court résumé affiché dans les listes d'articles"
          class="input-field resize-none"
        ></textarea>
      </div>

      <!-- Image de couverture -->
      <div class="card">
        <label class="block text-sm font-semibold text-gray-700 mb-3">
          Image de couverture <span class="text-gray-400 font-normal">(optionnel)</span>
        </label>

        <!-- Aperçu image actuelle -->
        <div v-if="currentCoverPreview" class="mb-4 relative inline-block">
          <img
            :src="currentCoverPreview"
            alt="Couverture"
            class="w-full max-w-sm h-48 object-cover rounded-lg border border-gray-200"
          />
          <button
            type="button"
            @click="removeCoverImage"
            class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center hover:bg-red-700 text-xs font-bold"
            title="Supprimer l'image"
          >
            ✕
          </button>
          <p class="text-xs text-gray-500 mt-1">
            {{ form.cover_image ? 'Nouvelle image sélectionnée' : 'Image actuelle' }}
          </p>
        </div>

        <!-- Zone de drop/upload -->
        <div
          class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition-all"
          @click="triggerFileInput"
          @dragover.prevent
          @drop.prevent="handleDrop"
        >
          <input
            ref="fileInput"
            type="file"
            accept="image/jpeg,image/png,image/gif,image/webp"
            class="hidden"
            @change="handleFileChange"
          />
          <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <p class="text-gray-600 font-medium">Cliquez ou glissez une image ici</p>
          <p class="text-gray-400 text-xs mt-1">JPEG, PNG, GIF, WebP — max 2 Mo</p>
        </div>
      </div>

      <!-- Contenu -->
      <div class="card">
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-semibold text-gray-700">
            Contenu <span class="text-red-500">*</span>
          </label>
          <span class="text-xs text-gray-400">{{ form.content.length }} caractères</span>
        </div>
        <textarea
          v-model="form.content"
          required
          rows="18"
          placeholder="Rédigez le contenu de votre article ici..."
          class="input-field resize-y font-mono text-sm"
        ></textarea>
      </div>

      <!-- Statut -->
      <div class="card">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Statut de publication</label>
        <div class="flex gap-6">
          <label class="flex items-center gap-3 cursor-pointer group">
            <input
              v-model="form.status"
              type="radio"
              value="draft"
              class="w-4 h-4 text-indigo-600 cursor-pointer"
            />
            <div>
              <span class="font-medium text-gray-800 group-hover:text-indigo-600">Brouillon</span>
              <p class="text-xs text-gray-500">Visible uniquement par vous</p>
            </div>
          </label>
          <label class="flex items-center gap-3 cursor-pointer group">
            <input
              v-model="form.status"
              type="radio"
              value="published"
              class="w-4 h-4 text-indigo-600 cursor-pointer"
            />
            <div>
              <span class="font-medium text-gray-800 group-hover:text-indigo-600">Publié</span>
              <p class="text-xs text-gray-500">Visible par tous les utilisateurs</p>
            </div>
          </label>
        </div>
      </div>

      <!-- Message d'erreur -->
      <div v-if="submitError" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span>{{ submitError }}</span>
      </div>

      <!-- Boutons d'action -->
      <div class="flex items-center gap-4 pb-8">
        <button
          type="submit"
          :disabled="isSaving || !form.title.trim() || !form.content.trim()"
          class="btn-primary min-w-48"
        >
          <span v-if="!isSaving" class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Enregistrer les modifications
          </span>
          <span v-else class="flex items-center gap-2">
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            Enregistrement...
          </span>
        </button>

        <NuxtLink :to="`/articles/${route.params.id}`" class="btn-secondary">
          Annuler
        </NuxtLink>

        <!-- Indicateur statut -->
        <span class="ml-auto text-sm text-gray-500">
          Statut actuel :
          <span :class="form.status === 'published' ? 'text-green-600 font-semibold' : 'text-yellow-600 font-semibold'">
            {{ form.status === 'published' ? '● Publié' : '● Brouillon' }}
          </span>
        </span>
      </div>

    </form>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const articleStore = useArticleStore()
const { success, error: showError } = useNotification()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase
const backendUrl = config.public.backendUrl

const getImageUrl = (path: string | null | undefined) => {
  if (!path) return null
  const cleanPath = path.replace(/^\/?storage\//, '')
  return `${backendUrl}/storage/${cleanPath}`
}

// Refs
const fileInput = ref<HTMLInputElement | null>(null)
const loadingArticle = ref(true)
const loadError = ref('')
const isSaving = ref(false)
const submitError = ref('')

// Formulaire
const form = reactive({
  title: '',
  excerpt: '',
  content: '',
  status: 'draft' as 'draft' | 'published',
  cover_image: null as File | null
})

// Aperçu de l'image de couverture (URL blob ou URL serveur)
const currentCoverPreview = ref<string | null>(null)
// URL originale sur le serveur (pour savoir si l'image a changé)
const originalCoverUrl = ref<string | null>(null)

// --- Gestion image ---

const triggerFileInput = () => fileInput.value?.click()

const handleFileChange = (e: Event) => {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) applyFile(file)
}

const handleDrop = (e: DragEvent) => {
  const file = e.dataTransfer?.files?.[0]
  if (file && file.type.startsWith('image/')) applyFile(file)
}

const applyFile = (file: File) => {
  if (file.size > 2 * 1024 * 1024) {
    showError('L\'image ne doit pas dépasser 2 Mo')
    return
  }
  form.cover_image = file
  // Libérer l'ancien blob si nécessaire
  if (currentCoverPreview.value && currentCoverPreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(currentCoverPreview.value)
  }
  currentCoverPreview.value = URL.createObjectURL(file)
}

const removeCoverImage = () => {
  form.cover_image = null
  if (currentCoverPreview.value && currentCoverPreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(currentCoverPreview.value)
  }
  currentCoverPreview.value = null
  if (fileInput.value) fileInput.value.value = ''
}

// --- Chargement ---

onMounted(async () => {
  const id = parseInt(route.params.id as string)
  if (isNaN(id)) {
    loadError.value = 'Identifiant d\'article invalide'
    loadingArticle.value = false
    return
  }

  const article = await articleStore.fetchArticleById(id)

  if (!article) {
    loadError.value = articleStore.error || 'Article introuvable'
    loadingArticle.value = false
    return
  }

  // Vérifier les droits
  if (!authStore.isAdmin && article.user_id !== authStore.currentUser?.id) {
    showError('Vous n\'êtes pas autorisé à modifier cet article')
    router.replace(`/articles/${id}`)
    return
  }

  // Pré-remplir le formulaire
  form.title   = article.title
  form.excerpt = article.excerpt ?? ''
  form.content = article.content
  form.status  = article.status

  // Aperçu de l'image existante
  if (article.cover_image) {
    // Avant : originalCoverUrl.value = `${apiBase}/storage/${article.cover_image}`
    // Après :
    originalCoverUrl.value = getImageUrl(article.cover_image)
    currentCoverPreview.value = originalCoverUrl.value
  }

  loadingArticle.value = false
})

// Libérer les blobs à la destruction
onUnmounted(() => {
  if (currentCoverPreview.value?.startsWith('blob:')) {
    URL.revokeObjectURL(currentCoverPreview.value)
  }
})

// --- Soumission ---

const submitArticle = async () => {
  submitError.value = ''
  isSaving.value = true

  const id = parseInt(route.params.id as string)

  const result = await articleStore.updateArticle(id, {
    title:       form.title,
    excerpt:     form.excerpt,
    content:     form.content,
    status:      form.status,
    cover_image: form.cover_image // null si inchangée ou supprimée
  })

  isSaving.value = false

  if (result) {
    success('Article modifié avec succès !')
    router.push(`/articles/${id}`)
  } else {
    submitError.value = articleStore.error || 'Une erreur est survenue, veuillez réessayer.'
  }
}

definePageMeta({})
</script>