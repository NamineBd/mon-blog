<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Créer un compte</h2>

    <form @submit.prevent="register" class="space-y-4">
      <!-- Nom -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
        <input
          v-model="form.name"
          type="text"
          required
          placeholder="Jean Dupont"
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
          placeholder="Minimum 8 caractères"
          class="input-field"
          min-length="8"
        />
      </div>

      <!-- Confirmation mot de passe -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer mot de passe</label>
        <input
          v-model="form.passwordConfirmation"
          type="password"
          required
          placeholder="Confirmez votre mot de passe"
          class="input-field"
          min-length="8"
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
        <span v-if="!authStore.isLoading">S'inscrire</span>
        <span v-else class="flex items-center gap-2">
          <div class="loading-spinner border-white border-t-indigo-600"></div>
          Inscription en cours...
        </span>
      </button>
    </form>

    <!-- Lien vers connexion -->
    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Vous avez déjà un compte?
        <NuxtLink to="/auth/login" class="text-indigo-600 font-semibold hover:underline">
          Se connecter
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
const { success, error } = useNotification()

const form = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: ''
})

const register = async () => {
  if (form.password !== form.passwordConfirmation) {
    error('Les mots de passe ne correspondent pas')
    return
  }

  const result = await authStore.register(
    form.name,
    form.email,
    form.password,
    form.passwordConfirmation
  )

  if (result) {
    success('Inscription réussie! Bienvenue')
    router.push('/articles')
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    router.push('/articles')
  }
})
</script>