<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
const authStore = useAuthStore()

// Charger la session dès que l'app démarre (côté client)
onMounted(() => {
  authStore.loadFromStorage()
})
</script>