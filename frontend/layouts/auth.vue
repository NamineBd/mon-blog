<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-indigo-600 mb-1">BlogHub</h1>
        <p class="text-gray-500 text-sm">Plateforme de blog moderne</p>
      </div>

      <!-- Carte -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <slot />
      </div>
    </div>

    <NotificationContainer />
  </div>
</template>
