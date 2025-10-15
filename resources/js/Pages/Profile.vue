<template>
  <MainLayout>
    <template #default>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <HeadTitle title="Your profile" />

            <div class="flex flex-col md:flex-row gap-8">
        <p>Your data will be here</p>
      </div>
            </div>
            </div>
            </div>
    </template>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import HeadTitle from '@/Components/HeadTitle.vue'
const { user } = defineProps(['user'])


</script>


