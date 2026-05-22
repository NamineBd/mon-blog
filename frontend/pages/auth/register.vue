<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Créer un compte</h2>
    <p class="text-gray-500 mb-6 text-sm">Rejoignez la communauté BlogHub dès maintenant</p>

    <form @submit.prevent="register" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
        <input
          v-model="form.name"
          type="text"
          required
          placeholder="Jean Dupont"
          class="input-field"
          autocomplete="name"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          placeholder="jean@example.com"
          class="input-field"
          autocomplete="email"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
        <input
          v-model="form.password"
          type="password"
          required
          minlength="8"
          placeholder="Minimum 8 caractères"
          class="input-field"
          autocomplete="new-password"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer mot de passe</label>
        <input
          v-model="form.passwordConfirmation"
          type="password"
          required
          minlength="8"
          placeholder="Confirmez votre mot de passe"
          class="input-field"
          autocomplete="new-password"
        />
      </div>

      <div v-if="authStore.error" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
        {{ authStore.error }}
      </div>

      <button
        type="submit"
        :disabled="authStore.isLoading"
        class="w-full btn-primary"
      >
        <span v-if="!authStore.isLoading">S'inscrire</span>
        <span v-else class="flex items-center justify-center gap-2">
          <div class="loading-spinner"></div>
          Inscription...
        </span>
      </button>
    </form>

    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Déjà un compte ?
        <NuxtLink to="/auth/login" class="text-indigo-600 font-semibold hover:underline">
          Se connecter
        </NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: ''
})

const register = async () => {
  authStore.setError(null)
  if (form.password !== form.passwordConfirmation) {
    authStore.setError('Les mots de passe ne correspondent pas')
    return
  }
  const ok = await authStore.register(form.name, form.email, form.password, form.passwordConfirmation)
  if (ok) router.push('/articles')
}
</script>
