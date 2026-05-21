<template>
  <div>
    <div class="mb-8">
      <h1 class="section-title">Abonnés newsletter</h1>
      <p class="section-subtitle">Consultez la liste des utilisateurs abonnés à votre newsletter</p>
    </div>

    <!-- Vérification des droits -->
    <div v-if="!authStore.isAdmin" class="card text-center py-12 text-red-600">
      Accès refusé
    </div>

    <!-- Tableau des abonnés -->
    <div v-else-if="newsletterStore.isLoading" class="space-y-4">
      <div v-for="i in 5" :key="i" class="h-20 card animate-pulse bg-gray-200"></div>
    </div>

    <div v-else-if="newsletterStore.subscribers.length > 0" class="card overflow-x-auto">
      <div class="mb-6 text-right">
        <p class="text-gray-600">
          Total: <span class="font-bold text-lg text-indigo-600">{{ newsletterStore.pagination.total }}</span> abonnés
        </p>
      </div>

      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Statut</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Abonné le</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="subscriber in newsletterStore.subscribers" :key="subscriber.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <a :href="`mailto:${subscriber.email}`" class="text-indigo-600 hover:underline font-medium">
                {{ subscriber.email }}
              </a>
            </td>
            <td class="px-6 py-4">
              <span v-if="subscriber.verified_at" class="badge badge-success">Vérifié</span>
              <span v-else class="badge badge-warning">En attente</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
              {{ formatDate(subscriber.created_at) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500">Aucun abonné pour le moment</p>
    </div>

    <!-- Pagination -->
    <div v-if="newsletterStore.pagination.last_page > 1" class="mt-8 flex justify-center gap-2">
      <button
        v-for="page in range(1, newsletterStore.pagination.last_page + 1)"
        :key="page"
        @click="goToPage(page)"
        :class="[
          'px-4 py-2 rounded-lg font-semibold transition',
          newsletterStore.pagination.current_page === page
            ? 'bg-indigo-600 text-white'
            : 'bg-white text-gray-700 border border-gray-200 hover:border-indigo-600'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const newsletterStore = useNewsletterStore()
const router = useRouter()

const goToPage = async (page: number) => {
  await newsletterStore.fetchSubscribers(page)
}

const range = (start: number, end: number) => {
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
}

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(new Date(date))
}

onMounted(async () => {
  authStore.loadFromStorage()

  if (!authStore.isAuthenticated || !authStore.isAdmin) {
    router.push('/auth/login')
    return
  }

  await newsletterStore.fetchSubscribers()
})

definePageMeta({
  middleware: 'auth'
})
</script>