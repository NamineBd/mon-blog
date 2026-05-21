<template>
  <div class="card">
    <!-- Header du commentaire -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center gap-3">
        <img
          :src="`https://ui-avatars.com/api/?name=${comment.user?.name}`"
          :alt="comment.user?.name"
          class="w-10 h-10 rounded-full"
        />
        <div>
          <p class="font-semibold text-gray-900">{{ comment.user?.name }}</p>
          <time :datetime="comment.created_at" class="text-sm text-gray-500">
            {{ formatDate(comment.created_at) }}
          </time>
        </div>
      </div>

      <!-- Actions -->
      <div v-if="canEdit" class="flex gap-2">
        <button
          v-if="!isEditing"
          @click="isEditing = true"
          class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm"
        >
          Modifier
        </button>
        <button
          @click="$emit('delete', comment.id)"
          class="text-red-600 hover:text-red-700 font-semibold text-sm"
        >
          Supprimer
        </button>
      </div>
    </div>

    <!-- Contenu -->
    <div v-if="!isEditing" class="text-gray-700 whitespace-pre-wrap">
      {{ comment.content }}
    </div>

    <!-- Formulaire d'édition -->
    <div v-else class="space-y-4">
      <textarea
        v-model="editedContent"
        class="textarea-field"
        rows="4"
      ></textarea>
      <div class="flex gap-2">
        <button
          @click="saveEdit"
          class="btn-primary btn-sm"
        >
          Enregistrer
        </button>
        <button
          @click="isEditing = false"
          class="btn-secondary btn-sm"
        >
          Annuler
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const isEditing = ref(false)
const editedContent = ref('')

interface Comment {
  id: number
  content: string
  created_at: string
  user?: {
    name: string
    email: string
  }
}

const props = defineProps<{
  comment: Comment
  canEdit: boolean
}>()

const emit = defineEmits<{
  update: [commentId: number, content: string]
  delete: [commentId: number]
}>()

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const saveEdit = () => {
  if (editedContent.value.trim()) {
    emit('update', props.comment.id, editedContent.value)
    isEditing.value = false
  }
}

watch(() => isEditing.value, (newVal) => {
  if (newVal) {
    editedContent.value = props.comment.content
  }
})
</script>