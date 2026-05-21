<template>
  <div>
    <h1 class="section-title mb-8">Panneau d'administration</h1>

    <!-- Vérification des droits -->
    <div v-if="!authStore.isAdmin" class="card text-center py-12">
      <p class="text-red-600 font-semibold">Accès refusé. Seuls les administrateurs peuvent accéder à cette section.</p>
      <NuxtLink to="/articles" class="btn-primary mt-6">
        Retour aux articles
      </NuxtLink>
    </div>

    <!-- Dashboard Admin -->
    <div v-else>
      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="card">
          <p class="text-gray-600 text-sm mb-2">Utilisateurs</p>
          <div class="text-3xl font-bold text-indigo-600">{{ userStore.pagination.total }}</div>
        </div>
        <div class="card">
          <p class="text-gray-600 text-sm mb-2">Articles</p>
          <div class="text-3xl font-bold text-indigo-600">{{ articleStore.pagination.total }}</div>
        </div>
        <div class="card">
          <p class="text-gray-600 text-sm mb-2">Abonnés</p>
          <div class="text-3xl font-bold text-indigo-600">{{ newsletterStore.pagination.total }}</div>
        </div>
        <div class="card">
          <p class="text-gray-600 text-sm mb-2">Dernière mise à jour</p>
          <div class="text-lg font-bold text-indigo-600">Aujourd'hui</div>
        </div>
      </div>

      <!-- Navigation -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <NuxtLink to="/admin/users" class="card-hover">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Gestion des utilisateurs</h3>
              <p class="text-gray-600">Créer, modifier ou supprimer des utilisateurs</p>
            </div>
            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
            </svg>
          </div>
        </NuxtLink>

        <NuxtLink to="/admin/subscribers" class="card-hover">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Abonnés newsletter</h3>
              <p class="text-gray-600">Consulter la liste des abonnés vérifiés</p>
            </div>
            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const userStore = useUserStore()
const articleStore = useArticleStore()
const newsletterStore = useNewsletterStore()
const router = useRouter()

onMounted(async () => {
  authStore.loadFromStorage()

  if (!authStore.isAuthenticated) {
    router.push('/auth/login')
    return
  }

  // Charger les données
  await userStore.fetchUsers(1)
  await articleStore.fetchArticles(1)
  await newsletterStore.fetchSubscribers(1)
})

definePageMeta({
  middleware: 'auth'
})
</script>