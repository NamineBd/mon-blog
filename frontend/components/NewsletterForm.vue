<template>
  <form @submit.prevent="subscribe" class="max-w-md mx-auto">
    <div class="flex gap-2">
      <input
        v-model="email"
        type="email"
        required
        placeholder="votre@email.com"
        class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
      />
      <button
        type="submit"
        :disabled="isLoading"
        class="btn-primary"
      >
        <span v-if="!isLoading">S'abonner</span>
        <span v-else class="flex items-center gap-2">
          <div class="loading-spinner border-white border-t-indigo-600"></div>
        </span>
      </button>
    </div>
    <p v-if="successMessage" class="text-green-600 text-sm mt-3 animate-fade-in">
      {{ successMessage }}
    </p>
  </form>
</template>

<script setup lang="ts">
const newsletterStore = useNewsletterStore()
const { success, error: showError } = useNotification()

const email = ref('')
const isLoading = ref(false)
const successMessage = ref('')

const subscribe = async () => {
  isLoading.value = true
  successMessage.value = ''

  const result = await newsletterStore.subscribe(email.value)

  if (result.success) {
    success(result.message)
    successMessage.value = 'Vérifiez votre email pour confirmer votre abonnement!'
    email.value = ''
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  } else {
    showError(result.message)
  }

  isLoading.value = false
}
</script>
