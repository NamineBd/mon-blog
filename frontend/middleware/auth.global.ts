export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()

  // Charger le token depuis localStorage (côté client uniquement)
  if (process.client) {
    authStore.loadFromStorage()
  }

  const publicPaths = ['/auth/login', '/auth/register']
  const isPublic = publicPaths.includes(to.path)

  // Non connecté → login
  if (!authStore.isAuthenticated && !isPublic) {
    return navigateTo('/auth/login')
  }

  // Déjà connecté → pas besoin d'être sur login/register
  if (authStore.isAuthenticated && isPublic) {
    return navigateTo('/articles')
  }

  // Route admin → vérifier is_admin
  if (to.path.startsWith('/admin') && !authStore.isAdmin) {
    return navigateTo('/articles')
  }
})
