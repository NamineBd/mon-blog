<template>
  <div>
    <h1 class="section-title mb-8">Mon profil</h1>

    <div class="max-w-2xl">
      <!-- Avatar et infos -->
      <div class="card mb-8">
        <div class="text-center mb-8">
          <img
            :src="`https://ui-avatars.com/api/?name=${authStore.currentUser?.name}&size=120`"
            :alt="authStore.currentUser?.name"
            class="w-24 h-24 rounded-full mx-auto mb-4"
          />
          <h2 class="text-2xl font-bold text-gray-900">{{ authStore.currentUser?.name }}</h2>
          <p class="text-gray-600">{{ authStore.currentUser?.email }}</p>
          <span v-if="authStore.isAdmin" class="badge badge-primary mt-2">Administrateur</span>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ myArticlesCount }}</div>
            <p class="text-sm text-gray-600">Articles</p>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ myCommentsCount }}</div>
            <p class="text-sm text-gray-600">Commentaires</p>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ creationDate }}</div>
            <p class="text-sm text-gray-600">Membre depuis</p>
          </div>
        </div>
      </div>

      <!-- Formulaire de modification -->
      <div class="card">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Modifier mon profil</h3>

        <form @submit.prevent="updateProfile" class="space-y-6">
          <!-- Nom -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="input-field"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input-field"
            />
          </div>

          <!-- Section changement de mot de passe -->
          <div class="border-t pt-6">
            <h4 class="font-semibold text-gray-900 mb-4">Changer le mot de passe</h4>

            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel</label>
              <input
                v-model="passwordForm.currentPassword"
                type="password"
                class="input-field"
              />
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
              <input
                v-model="passwordForm.newPassword"
                type="password"
                min-length="8"
                class="input-field"
              />
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
              <input
                v-model="passwordForm.confirmPassword"
                type="password"
                min-length="8"
                class="input-field"
              />
            </div>
          </div>

          <!-- Erreur -->
          <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
            {{ error }}
          </div>

          <!-- Message de succès -->
          <div v-if="successMessage" class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-600 text-sm">
            {{ successMessage }}
          </div>

          <!-- Boutons -->
          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              :disabled="isLoading"
              class="btn-primary flex-1"
            >
              <span v-if="!isLoading">Enregistrer les modifications</span>
              <span v-else class="flex items-center justify-center gap-2">
                <div class="loading-spinner border-white border-t-indigo-600"></div>
              </span>
            </button>
            <NuxtLink to="/articles" class="btn-secondary flex-1">
              Annuler
            </NuxtLink>
          </div>
        </form>
      </div>

      <!-- Zone danger -->
      <div class="card border-red-200 bg-red-50 mt-8">
        <h3 class="text-xl font-bold text-red-900 mb-4">Zone danger</h3>
        <p class="text-red-700 mb-4">
          Les actions dans cette zone ne peuvent pas être annulées. Veuillez procéder avec prudence.
        </p>
        <button
          @click="logout"
          class="w-full btn-danger"
        >
          Déconnexion
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const articleStore = useArticleStore()
const router = useRouter()
const { success, error: showError } = useNotification()

const form = reactive({
  name: '',
  email: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const isLoading = ref(false)
const error = ref('')
const successMessage = ref('')

const myArticlesCount = computed(() => {
  return articleStore.articles.filter(a => a.user_id === authStore.currentUser?.id).length
})

const myCommentsCount = computed(() => {
  return 0 // À implémenter avec un store de commentaires
})

const creationDate = computed(() => {
  if (!authStore.currentUser?.created_at) return '...'
  return new Date(authStore.currentUser.created_at).getFullYear()
})

const updateProfile = async () => {
  error.value = ''
  successMessage.value = ''

  if (passwordForm.newPassword && passwordForm.newPassword !== passwordForm.confirmPassword) {
    error.value = 'Les nouveaux mots de passe ne correspondent pas'
    return
  }

  isLoading.value = true

  try {
    const config = useRuntimeConfig()
    const body: any = {
      name: form.name,
      email: form.email
    }

    if (passwordForm.newPassword) {
      body.current_password = passwordForm.currentPassword
      body.password = passwordForm.newPassword
      body.password_confirmation = passwordForm.confirmPassword
    }

    const response = await $fetch(`${config.public.apiBase}/users/${authStore.currentUser?.id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      },
      body
    })

    authStore.currentUser = response.user
    successMessage.value = 'Profil modifié avec succès!'
    passwordForm.currentPassword = ''
    passwordForm.newPassword = ''
    passwordForm.confirmPassword = ''

    // Sauvegarder dans localStorage
    if (process.client) {
      localStorage.setItem('user', JSON.stringify(response.user))
    }
  } catch (err: any) {
    error.value = err.data?.message || 'Erreur lors de la modification'
  } finally {
    isLoading.value = false
  }
}

const logout = async () => {
  if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
    await authStore.logout()
    success('Vous avez été déconnecté')
    router.push('/auth/login')
  }
}

onMounted(() => {
  authStore.loadFromStorage()

  if (!authStore.isAuthenticated) {
    router.push('/auth/login')
    return
  }

  form.name = authStore.currentUser?.name || ''
  form.email = authStore.currentUser?.email || ''
})

definePageMeta({
  // middleware: 'auth'
})
</script>
