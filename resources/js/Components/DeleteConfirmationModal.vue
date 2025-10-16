<!-- DeleteConfirmModal.vue -->
<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
      <h2 class="text-lg font-semibold mb-4">{{ title }}</h2>
      <p>{{ message }}</p>

      <div class="mt-6 flex justify-end space-x-4">
        <button @click="$emit('cancel')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
        <button @click="confirmDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Delete</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean,
  title: { type: String, default: 'Confirm Deletion' },
  message: { type: String, default: 'Are you sure you want to delete this item?' },
  routeName: String,
  routeParams: Object
})

const emit = defineEmits(['cancel', 'deleted'])

function confirmDelete() {
  router.delete(route(props.routeName, props.routeParams), {
    onSuccess: () => {
      emit('deleted')
    },
  })
}
</script>
