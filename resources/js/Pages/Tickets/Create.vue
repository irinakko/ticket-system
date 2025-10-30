<template>
  <MainLayout>
    <template #default>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <HeadTitle title="Create Ticket" />
            <TicketForm 
              :users="users" 
              :description="description"
              :statuses="statuses" 
              :priorities="priorities"
              :categories="categories"
              :labels="labels"
            />
            </div>
            </div>
            </div>
    </template>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import HeadTitle from '@/Components/HeadTitle.vue'
import TicketForm from '../../Components/TicketForm.vue'

defineProps({
  categories: Array,
  description: String,
  labels: Array,
  users: Array,
  statuses: Array,
  priorities: Array,
})

</script>