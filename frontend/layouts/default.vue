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
            <NuxtLink to="/articles" class="text-gray-700 hover:text-indigo-600 font-medium">
              Articles
            </NuxtLink>
            <NuxtLink to="/articles/create" class="text-gray-700 hover:text-indigo-600 font-medium">
              Écrire
            </NuxtLink>
          </div>

          <!-- Droite : Admin + User -->
          <div class="flex items-center gap-4">
            <!-- Lien Admin (si admin) -->
            <NuxtLink
              v-if="authStore.isAdmin"
              to="/admin"
              class="text-indigo-600 font-semibold hover:underline text-sm"
            >
              ⚙ Admin
            </NuxtLink>

            <!-- Menu utilisateur -->
            <div class="relative" ref="userMenuRef">
              <button
                @click="menuOpen = !menuOpen"
                class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 font-medium text-sm"
              >
                <img
                  :src="`https://ui-avatars.com/api/?name=${authStore.currentUser?.name}&size=32`"
                  class="w-8 h-8 rounded-full"
                  alt="avatar"
                />
                <span class="hidden md:block max-w-32 truncate">{{ authStore.currentUser?.name }}</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>

              <!-- Dropdown -->
              <Transition name="dropdown">
                <div
                  v-if="menuOpen"
                  class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50"
                >
                  <NuxtLink
                    to="/articles/mine"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50"
                    @click="menuOpen = false"
                  >
                    Mes articles
                  </NuxtLink>
                  <NuxtLink
                    to="/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50"
                    @click="menuOpen = false"
                  >
                    Mon profil
                  </NuxtLink>
                  <div class="border-t border-gray-100 my-1"></div>
                  <button
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                  >
                    Déconnexion
                  </button>
                </div>
              </Transition>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <slot />
    </main>

    <!-- Notifications -->
    <NotificationContainer />
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()
const menuOpen = ref(false)
const userMenuRef = ref<HTMLElement | null>(null)

// Fermer le menu si clic à l'extérieur
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (userMenuRef.value && !userMenuRef.value.contains(e.target as Node)) {
      menuOpen.value = false
    }
  })
})

const logout = async () => {
  menuOpen.value = false
  await authStore.logout()
  router.push('/auth/login')
}
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.15s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
