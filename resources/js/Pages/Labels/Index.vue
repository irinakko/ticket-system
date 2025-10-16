<template>
  <MainLayout>
    <Table
      title="Labels"
      routeBase="labels"
      :items="labels"
      :filters="filters"
      @edit="openModal($event)"
      @delete="deleteLabel"
      @open-create-modal="openModal()"
    />

    <Modal
      :show="showModal"
      :title="modalTitle"
      :initialData="form"
      @close="showModal = false"
      @submit="submit"
    >
      <template #form="{ form }">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Name</label>
          <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md" required />
        </div>
      </template>
    </Modal>
  <div
    v-if="successMessage"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]"
  >
    <div
      class="bg-green-100 border border-green-400 text-green-800 px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3"
    >
      <span class="text-sm font-medium">{{ successMessage }}</span>
      <button @click="successMessage = ''" class="text-green-700 hover:text-green-900 font-bold text-lg leading-none">&times;</button>
    </div>
  </div>
  </MainLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import Table from '@/Components/Table.vue'
import { route } from 'ziggy-js'
import MainLayout from '../../Layouts/MainLayout.vue'

const props = defineProps({
  labels: Array,
  filters: Object,
})

const showModal = ref(false)
const modalTitle = ref('')


const form = reactive({ name: '' })
let editingId = null

function openModal(item = null) {
  if (item) {
    modalTitle.value = 'Edit Label'
    Object.assign(form, item)
    editingId = item.id
    console.log('Opening modal for edit with:', JSON.stringify(form))
  } else {
    modalTitle.value = 'Add Label'
    Object.assign(form, { name: '' })
    editingId = null
  }
  showModal.value = true
}

function submit(data) {
  if (editingId) {
    router.put(route('labels.update', editingId), data, {
      onSuccess: () => {
        showModal.value = false
      },
    })
  } else {
    router.post(route('labels.store'), data, {
      onSuccess: () => {
        showModal.value = false
      },
    })
  }
}
const successMessage = ref('')

function deleteLabel(item) {
  if (!confirm(`Are you sure you want to delete "${item.name}"?`)) {
    return
  }

  router.delete(route('labels.destroy', item.id), {
    onSuccess: () => {
      successMessage.value = 'Deleted successfully!'
      setTimeout(() => {
        successMessage.value = ''
      }, 3000) 
    },
    onError: () => {
      successMessage.value = 'Failed to delete.'
    },
  })
}
</script>


