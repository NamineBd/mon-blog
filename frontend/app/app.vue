<template>
  <div>
    <NuxtPage />
  </div>
</template>

<script setup lang="ts">
// Charger l'authentification au démarrage
const authStore = useAuthStore()

onMounted(() => {
  authStore.loadFromStorage()
})
</script>