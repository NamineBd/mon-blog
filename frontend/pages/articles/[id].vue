<template>
  <div>
    <div v-if="articleStore.isLoading" class="text-center py-12">
      <div class="inline-block loading-spinner border-4 border-indigo-200 border-t-indigo-600"></div>
    </div>

    <template v-else-if="article">
      <!-- Header -->
      <div class="mb-8">
        <NuxtLink to="/articles" class="text-indigo-600 hover:underline mb-4 inline-block">
          ← Retour aux articles
        </NuxtLink>

        <h1 class="section-title mb-4">{{ article.title }}</h1>

        <!-- Meta -->
        <div class="flex items-center justify-between flex-wrap gap-4 text-gray-600">
          <div class="flex items-center gap-4">
            <img
              :src="`https://ui-avatars.com/api/?name=${article.user?.name}`"
              :alt="article.user?.name"
              class="w-10 h-10 rounded-full"
            />
            <div>
              <p class="font-semibold text-gray-900">{{ article.user?.name }}</p>
              <time :datetime="article.published_at">
                {{ formatDate(article.published_at || article.created_at) }}
              </time>
            </div>
          </div>

          <!-- Actions (propriétaire ou admin) -->
          <div v-if="canEditArticle" class="flex gap-2">
            <NuxtLink
              :to="`/articles/${article.id}/edit`"
              class="btn-outline btn-sm"
            >
              Modifier
            </NuxtLink>
            <button
              @click="deleteArticle"
              class="btn-danger btn-sm"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>

      <!-- Image de couverture -->
      <div v-if="article.cover_image" class="mb-8">
        <img
          :src="getImageUrl(article.cover_image)"
          :alt="article.title"
          class="w-full h-96 object-cover rounded-lg"
        />
      </div>

      <!-- Contenu -->
      <article class="prose prose-lg max-w-none mb-12 bg-white rounded-lg p-8 border border-gray-200">
        <div class="whitespace-pre-wrap text-gray-800 leading-relaxed">
          {{ article.content }}
        </div>
      </article>

      <!-- Images additionnelles -->
      <div v-if="article.images?.length > 0" class="mb-12">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Images de l'article</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="image in article.images"
            :key="image.id"
            class="relative group"
          >
            <img
              :src="getImageUrl(image.image_path)"
              :alt="article.title"
              class="w-full h-64 object-cover rounded-lg"
            />
            <button
              v-if="canEditArticle"
              @click="deleteImage(image.id)"
              class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-lg opacity-0 group-hover:opacity-100 transition"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Upload d'images (si propriétaire) -->
      <div v-if="canEditArticle" class="mb-12">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Ajouter une image</h3>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-500 transition">
          <input
            ref="imageInput"
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            class="hidden"
          />
          <button
            type="button"
            @click="$refs.imageInput?.click()"
            class="inline-block"
          >
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <p class="text-gray-600">Cliquez pour télécharger une image</p>
          </button>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Commentaires -->
      <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Commentaires ({{ article.comments?.length || 0 }})
        </h2>

        <!-- Formulaire de commentaire -->
        <div class="mb-8 card">
          <form @submit.prevent="submitComment">
            <textarea
              v-model="newComment"
              placeholder="Votre commentaire..."
              class="textarea-field mb-4"
              rows="4"
            ></textarea>
            <button
              type="submit"
              :disabled="commentStore.isLoading || !newComment.trim()"
              class="btn-primary"
            >
              <span v-if="!commentStore.isLoading">Commenter</span>
              <span v-else class="flex items-center gap-2">
                <div class="loading-spinner border-white border-t-indigo-600"></div>
              </span>
            </button>
          </form>
        </div>

        <!-- Liste des commentaires -->
        <div v-if="article.comments?.length > 0" class="space-y-6">
          <CommentItem
            v-for="comment in article.comments"
            :key="comment.id"
            :comment="comment"
            :can-edit="canEditComment(comment)"
            @update="updateComment"
            @delete="deleteComment"
          />
        </div>

        <div v-else class="card text-center py-12 text-gray-500">
          Aucun commentaire pour le moment. Soyez le premier!
        </div>
      </section>
    </template>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500">Article non trouvé</p>
      <NuxtLink to="/articles" class="btn-primary mt-6">
        Retour aux articles
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const articleStore = useArticleStore()
const commentStore = useCommentStore()
const { success, error } = useNotification()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase
const backendUrl = config.public.backendUrl

const article = computed(() => articleStore.currentArticle)
const newComment = ref('')

const canEditArticle = computed(() => {
  if (!article.value || !authStore.currentUser) return false
  return authStore.isAdmin || article.value.user_id === authStore.currentUser.id
})

const getImageUrl = (path: string | null | undefined) => {
  if (!path) return null
  // Nettoie le chemin au cas où il contiendrait déjà 'storage/'
  const cleanPath = path.replace(/^\/?storage\//, '')
  return `${backendUrl}/storage/${cleanPath}`
}

const canEditComment = (comment: any) => {
  if (!authStore.currentUser) return false
  return authStore.isAdmin || comment.user_id === authStore.currentUser.id
}

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const handleImageUpload = async (e: Event) => {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]

  if (file && article.value) {
    const result = await articleStore.uploadArticleImage(article.value.id, file)
    if (result) {
      success('Image ajoutée avec succès!')
      input.value = ''
    }
  }
}

const submitComment = async () => {
  if (!article.value || !newComment.value.trim()) return

  const result = await commentStore.createComment(article.value.id, newComment.value)
  if (result) {
    success('Commentaire créé!')
    newComment.value = ''
    // Recharger l'article pour voir le nouveau commentaire
    await articleStore.fetchArticleById(article.value.id)
  }
}

const updateComment = async (commentId: number, content: string) => {
  const result = await commentStore.updateComment(commentId, content)
  if (result) {
    success('Commentaire modifié!')
    if (article.value) {
      await articleStore.fetchArticleById(article.value.id)
    }
  }
}

const deleteComment = async (commentId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')) {
    const result = await commentStore.deleteComment(commentId)
    if (result) {
      success('Commentaire supprimé!')
      if (article.value) {
        await articleStore.fetchArticleById(article.value.id)
      }
    }
  }
}

const deleteImage = async (imageId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette image?')) {
    const result = await articleStore.deleteArticleImage(imageId)
    if (result) {
      success('Image supprimée!')
    }
  }
}

const deleteArticle = async () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet article? Cette action est irréversible.')) {
    if (article.value) {
      const result = await articleStore.deleteArticle(article.value.id)
      if (result) {
        success('Article supprimé!')
        router.push('/articles')
      }
    }
  }
}

onMounted(async () => {
  authStore.loadFromStorage()

  if (!authStore.isAuthenticated) {
    router.push('/auth/login')
    return
  }

  const id = parseInt(route.params.id as string)
  await articleStore.fetchArticleById(id)
  await commentStore.fetchArticleComments(id)
})

definePageMeta({
  // middleware: 'auth'
})
</script>
