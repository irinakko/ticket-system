<template>
  <MainLayout>
    <template #default>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
              Dashboard
            </h2>

            <div class="flex flex-col md:flex-row gap-8">
              <!-- Tickets Summary -->
              <div class="flex-1">
                <h3 class="font-bold mb-2">Tickets</h3>
                <ul>
                  <li
                    v-for="(count, status) in ticketCounts"
                    :key="status"
                    class="flex items-center space-x-2 mb-2"
                  >
                    <span
                      class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-red-600"
                    >
                      <Ticket class="w-4 h-4" />
                    </span>
                    <span>{{ status }}: {{ count }}</span>
                  </li>
                </ul>
              </div>

              <!-- Users Summary -->
              <div class="flex-1">
                <h3 class="font-bold mb-2">Users</h3>
                <ul>
                  <li
                    v-for="(count, role) in userCounts"
                    :key="role"
                    class="flex items-center space-x-2 mb-2"
                  >
                    <span
                      class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-black-600"
                    >
                      <User class="w-4 h-4" />
                    </span>
                    <span>{{ role }}: {{ count }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Ticket, User } from 'lucide-vue-next'

defineProps({
  ticketCounts: Object,
  userCounts: Object,
  user: Object,
})
</script>


