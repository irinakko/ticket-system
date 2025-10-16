<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 overflow-visible border border-indigo-400">
    
      <header class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700">&times;</button>
      </header>

     
      <form @submit.prevent="handleSubmit">
       <!-- "$emit('submit', localForm)"> -->
        <slot name="form" :form="localForm" />

        <footer class="flex justify-end mt-6 space-x-2">
          <button
            type="button"
            @click="emit('close')"
            class="px-4 py-2 text-sm bg-gray-100 rounded hover:bg-gray-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm bg-indigo-600 rounded hover:bg-indigo-700"
          >
            Save
          </button>
        </footer>
      </form>
    </div>
  </div>
</template>


<script setup>
import { reactive, watch, nextTick } from 'vue'
const emit = defineEmits(['submit', 'close'])

const props = defineProps({
  show: Boolean,
  title: String,
  initialData: { type: Object, default: () => ({}) },
})

const localForm = reactive({
  id: null,
  name: '',
})

function handleSubmit() {
  console.log('Submitting from modal:', JSON.stringify(localForm))
  emit('submit', localForm)
}

watch(
  () => props.initialData,
  async (newData) => {
    await nextTick()
    Object.keys(localForm).forEach((key) => delete localForm[key])
    Object.assign(localForm, JSON.parse(JSON.stringify(newData || {})))
  },
  { immediate: true, deep: true }
)
</script>
