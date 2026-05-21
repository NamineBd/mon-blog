<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <NuxtLink to="/" class="text-2xl font-bold text-indigo-600 hover:text-indigo-700">
            BlogHub
          </NuxtLink>

          <!-- Menu central -->
          <div class="hidden md:flex gap-8">
            <NuxtLink 
              to="/articles" 
              class="text-gray-700 hover:text-indigo-600 transition font-medium"
            >
              Articles
            </NuxtLink>
            <a 
              href="#newsletter" 
              class="text-gray-700 hover:text-indigo-600 transition font-medium"
            >
              Newsletter
            </a>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-4">
            <!-- Admin Panel (si admin) -->
            <div v-if="authStore.isAdmin">
              <NuxtLink 
                to="/admin" 
                class="text-gray-700 hover:text-indigo-600 transition font-medium"
              >
                Admin
              </NuxtLink>
            </div>

            <!-- User Menu -->
            <div class="relative group">
              <button class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 transition font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                <span>{{ authStore.currentUser?.name || 'Compte' }}</span>
              </button>

              <!-- Dropdown Menu -->
              <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                <NuxtLink 
                  to="/profile" 
                  class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 rounded-t-lg"
                >
                  Mon profil
                </NuxtLink>
                <button 
                  @click="logout" 
                  class="w-full text-left px-4 py-2 text-gray-700 hover:bg-red-50 rounded-b-lg border-t"
                >
                  Déconnexion
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <slot />
    </main>

    <!-- Notifications -->
    <NotificationContainer />
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
  await authStore.logout()
  router.push('/auth/login')
}

onMounted(() => {
  // Charger les données de l'utilisateur depuis le localStorage
  authStore.loadFromStorage()
})
</script>