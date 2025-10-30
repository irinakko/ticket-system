<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['click'])

const singularTitle = computed(() => {
  if (props.title.endsWith('ies')) {
    return props.title.slice(0, -3) + 'y'
  }
  return props.title.endsWith('s') ? props.title.slice(0, -1) : props.title
})
</script>

<template>
  <button
    @click="emit('click')"
    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md
           font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
  >
    Add new {{ singularTitle }}
  </button>
</template>