<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">BlogHub</h1>
        <p class="text-gray-600">Plateforme de blog moderne et intuitive</p>
      </div>

      <!-- Card de contenu -->
      <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <slot />
      </div>

      <!-- Footer -->
      <div class="text-center text-sm text-gray-600">
        <p>© 2025 BlogHub. Tous droits réservés.</p>
      </div>
    </div>

    <!-- Notifications -->
    <NotificationContainer />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})
</script>