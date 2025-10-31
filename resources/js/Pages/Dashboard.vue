<template>
  <MainLayout>
    <template #default>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
           <div class="grid auto-rows-min gap-4 md:grid-cols-3">
              <div class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                <Doughnut :data="ticketsByStatus" class="mx-auto" />
              </div>
              <div
                class="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col items-center justify-start overflow-hidden rounded-xl border py-4"
              >
                    <Bar :data="usersByRole" class="mx-auto" />
                </div>
                <div class="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col items-center justify-start overflow-hidden rounded-xl border py-4">
                    <Line :data="ticketsByCreatedAt" class="mx-auto" />
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
import { Chart, registerables } from 'chart.js'
import { Bar, Doughnut, Line } from 'vue-chartjs';
Chart.register(...registerables);


const props = defineProps({
  user: { type: Object, default: () => ({}) },
  ticketsByStatus: { type: Object, default: () => ({}) },
  ticketsByCreatedAt: { type: Object, default: () => ({}) },
  usersByRole: { type: Object, default: () => ({}) },
});

console.log('ticketsByStatus:', props.ticketsByStatus);
console.log('usersByRole:', props.usersByRole);
console.log('ticketsByCreatedAt:', props.ticketsByCreatedAt);

</script>


