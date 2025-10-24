<template>
  <MainLayout>
    <Table
      title="Tickets"
      routeBase="tickets"
      :items="tickets"
      :filters="filterOptions"     
      :applied-filters="filters" 
      @apply-filters="applyFilters"
      @edit="openModal($event)"
      @delete="deleteTicket"
      @open-create-modal="openModal()">

    <template #extraHeaders>
        <th class="px-6 py-3 text-left text-sm font-semibold">Created by</th>
        <th class="px-6 py-3 text-left text-sm font-semibold">Assigned To</th>
        <th class="px-6 py-3 text-left text-sm font-semibold">Categories</th>
        <th class="px-6 py-3 text-left text-sm font-semibold">Labels</th>
        <th class="px-6 py-3 text-left text-sm font-semibold">Priority</th>
        <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
      </template>
      <template #extraColumns="{ item }">
        <td class="px-6 py-4" v-if="item.created_by">{{ item.created_by.name }}</td>
        <td class="px-6 py-4" v-if="item.assigned_to">{{ item.assigned_to.name }}</td>
        <td class="px-6 py-4">{{ item.categories.join(', ') }}</td>
        <td class="px-6 py-4">{{ item.labels.join(', ') }}</td>
        <td class="px-6 py-4">{{ item.priority }}</td>
        <td class="px-6 py-4">
          <span
            class="px-2 py-1 text-xs rounded font-medium"
            :class="{
              'bg-green-100 text-orange-700': item.status === 'to do',
              'bg-yellow-100 text-yellow-700': item.status === 'backlog',
              'bg-gray-100 text-green-700': item.status === 'in review',
            }"
          >
            {{ item.status }}
          </span>
        </td>
      </template>
      </Table>

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
  tickets: { type: Object, required: true }, 
  filters: { type: Object, default: () => ({}) }, 
  filterOptions: { type: Object, default: () => ({}) },
})

const showModal = ref(false)
const modalTitle = ref('')

function applyFilters(newFilters) {
  router.get(route('tickets.index'), { filters: newFilters }, {
    preserveState: true,
    replace: true,
  })
}
const form = reactive({ name: '' })
let editingId = null

function openModal(item = null) {
  if (item) {
    modalTitle.value = 'Edit Ticket'
    Object.assign(form, item)
    editingId = item.id
    console.log('Opening modal for edit with:', JSON.stringify(form))
  } else {
    modalTitle.value = 'Add Ticket'
    Object.assign(form, { name: '' })
    editingId = null
  }
  showModal.value = true
}

function submit(data) {
  if (editingId) {
    router.put(route('tickets.update', editingId), data, {
      onSuccess: () => {
        showModal.value = false
      },
    })
  } else {
    router.post(route('tickets.store'), data, {
      onSuccess: () => {
        showModal.value = false
      },
    })
  }
}
const successMessage = ref('')

function deleteTicket(item) {
  if (!confirm(`Are you sure you want to delete "${item.name}"?`)) {
    return
  }

  router.delete(route('tickets.destroy', item.id), {
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




