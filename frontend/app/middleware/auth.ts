export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  authStore.loadFromStorage()

  // Rediriger vers login si pas authentifié
  if (!authStore.isAuthenticated && to.path !== '/auth/login' && to.path !== '/auth/register') {
    return navigateTo('/auth/login')
  }

  // Rediriger vers articles si déjà authentifié et accès à auth
  if (authStore.isAuthenticated && (to.path === '/auth/login' || to.path === '/auth/register')) {
    return navigateTo('/articles')
  }

  // Protéger les routes admin
  if (to.path.startsWith('/admin') && !authStore.isAdmin) {
    return navigateTo('/articles')
  }
})