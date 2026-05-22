export default defineNuxtRouteMiddleware((to) => {
  // Ce middleware est global (fichier dans /middleware/ sans .global suffix = appelé manuellement)
  // Pour le rendre global, on le renomme auth.global.ts
  const authStore = useAuthStore()

  // Charger depuis localStorage à chaque navigation
  if (process.client) {
    authStore.loadFromStorage()
  }

  const publicRoutes = ['/auth/login', '/auth/register']
  const isPublicRoute = publicRoutes.includes(to.path)

  // Pas authentifié → rediriger vers login sauf si déjà sur page publique
  if (!authStore.isAuthenticated && !isPublicRoute) {
    return navigateTo('/auth/login')
  }

  // Déjà authentifié → rediriger vers articles si on accède au login/register
  if (authStore.isAuthenticated && isPublicRoute) {
    return navigateTo('/articles')
  }

  // Routes admin → rediriger si pas admin
  if (to.path.startsWith('/admin') && !authStore.isAdmin) {
    return navigateTo('/articles')
  }
})