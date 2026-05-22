<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Se connecter</h2>
    <p class="text-gray-500 mb-6 text-sm">Entrez vos identifiants pour accéder à votre espace</p>

    <form @submit.prevent="login" class="space-y-4">
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
          placeholder="Votre mot de passe"
          class="input-field"
          autocomplete="current-password"
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
        <span v-if="!authStore.isLoading">Se connecter</span>
        <span v-else class="flex items-center justify-center gap-2">
          <div class="loading-spinner"></div>
          Connexion...
        </span>
      </button>
    </form>

    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Pas encore de compte ?
        <NuxtLink to="/auth/register" class="text-indigo-600 font-semibold hover:underline">
          S'inscrire
        </NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()

const form = reactive({ email: '', password: '' })

const login = async () => {
  authStore.setError(null)
  const ok = await authStore.login(form.email, form.password)
  if (ok) router.push('/articles')
}
</script>
