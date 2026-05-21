<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Se connecter</h2>

    <form @submit.prevent="login" class="space-y-4">
      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          placeholder="jean@example.com"
          class="input-field"
        />
      </div>

      <!-- Mot de passe -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
        <input
          v-model="form.password"
          type="password"
          required
          placeholder="Votre mot de passe"
          class="input-field"
        />
      </div>

      <!-- Erreur -->
      <div v-if="authStore.error" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
        {{ authStore.error }}
      </div>

      <!-- Bouton -->
      <button
        type="submit"
        :disabled="authStore.isLoading"
        class="w-full btn-primary"
      >
        <span v-if="!authStore.isLoading">Se connecter</span>
        <span v-else class="flex items-center gap-2">
          <div class="loading-spinner border-white border-t-indigo-600"></div>
          Connexion en cours...
        </span>
      </button>
    </form>

    <!-- Lien vers inscription -->
    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Vous n'avez pas de compte?
        <NuxtLink to="/auth/register" class="text-indigo-600 font-semibold hover:underline">
          S'inscrire
        </NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

const authStore = useAuthStore()
const router = useRouter()
const { success, error: showError } = useNotification()

const form = reactive({
  email: '',
  password: ''
})

const login = async () => {
  const result = await authStore.login(form.email, form.password)

  if (result) {
    success('Connexion réussie! Bienvenue')
    router.push('/articles')
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    router.push('/articles')
  }
})
</script>