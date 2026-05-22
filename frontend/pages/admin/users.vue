<template>
  <div>
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="section-title">Gestion des utilisateurs</h1>
        <p class="section-subtitle">Créez, modifiez ou supprimez des comptes utilisateur</p>
      </div>
      <button @click="showCreateModal = true" class="btn-primary">
        + Nouvel utilisateur
      </button>
    </div>

    <!-- Vérification des droits -->
    <div v-if="!authStore.isAdmin" class="card text-center py-12 text-red-600">
      Accès refusé
    </div>

    <!-- Modal de création -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-8 max-w-md w-full m-4">
        <h2 class="text-2xl font-bold mb-6">Créer un utilisateur</h2>

        <form @submit.prevent="createUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
            <input
              v-model="newUserForm.name"
              type="text"
              required
              class="input-field"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="newUserForm.email"
              type="email"
              required
              class="input-field"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
            <input
              v-model="newUserForm.password"
              type="password"
              required
              min-length="8"
              class="input-field"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer mot de passe</label>
            <input
              v-model="newUserForm.passwordConfirmation"
              type="password"
              required
              min-length="8"
              class="input-field"
            />
          </div>

          <label class="flex items-center cursor-pointer">
            <input
              v-model="newUserForm.isAdmin"
              type="checkbox"
              class="w-4 h-4 text-indigo-600"
            />
            <span class="ml-2 text-gray-700">Administrateur</span>
          </label>

          <div class="flex gap-2 pt-4">
            <button
              type="submit"
              :disabled="userStore.isLoading"
              class="flex-1 btn-primary"
            >
              Créer
            </button>
            <button
              type="button"
              @click="showCreateModal = false"
              class="flex-1 btn-secondary"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div v-else-if="userStore.isLoading" class="space-y-4">
      <div v-for="i in 5" :key="i" class="h-20 card animate-pulse bg-gray-200"></div>
    </div>

    <div v-else-if="userStore.users.length > 0" class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nom</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rôle</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Inscrit</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="user in userStore.users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img
                  :src="`https://ui-avatars.com/api/?name=${user.name}`"
                  :alt="user.name"
                  class="w-8 h-8 rounded-full"
                />
                <span class="font-medium text-gray-900">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">{{ user.email }}</td>
            <td class="px-6 py-4">
              <span v-if="user.is_admin" class="badge badge-primary">Admin</span>
              <span v-else class="badge bg-gray-100 text-gray-700">User</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4">
              <button
                v-if="user.id !== authStore.currentUser?.id"
                @click="deleteUserConfirm(user.id)"
                class="text-red-600 hover:text-red-700 font-semibold text-sm"
              >
                Supprimer
              </button>
              <span v-else class="text-gray-400 text-sm">N/A</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="userStore.pagination.last_page > 1" class="mt-8 flex justify-center gap-2">
      <button
        v-for="page in range(1, userStore.pagination.last_page + 1)"
        :key="page"
        @click="goToPage(page)"
        :class="[
          'px-4 py-2 rounded-lg font-semibold transition',
          userStore.pagination.current_page === page
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
const userStore = useUserStore()
const router = useRouter()
const { success, error } = useNotification()

const showCreateModal = ref(false)
const newUserForm = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
  isAdmin: false
})

const createUser = async () => {
  if (newUserForm.password !== newUserForm.passwordConfirmation) {
    error('Les mots de passe ne correspondent pas')
    return
  }

  const result = await userStore.createUser(newUserForm)
  if (result) {
    success('Utilisateur créé avec succès!')
    showCreateModal.value = false
    newUserForm.name = ''
    newUserForm.email = ''
    newUserForm.password = ''
    newUserForm.passwordConfirmation = ''
    newUserForm.isAdmin = false
  }
}

const deleteUserConfirm = async (userId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')) {
    const result = await userStore.deleteUser(userId)
    if (result) {
      success('Utilisateur supprimé!')
    }
  }
}

const goToPage = async (page: number) => {
  await userStore.fetchUsers(page)
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

  await userStore.fetchUsers()
})

definePageMeta({
  middleware: 'auth'
})
</script>
