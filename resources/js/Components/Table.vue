<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow sm:rounded-lg p-6 text-gray-900">

        <div v-if="showCreate" class="mb-4">
            <button
          @click="$emit('open-create-modal')"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md
           font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
  >
    Add new {{ singularTitle }}
  </button>
        </div>

        <form @submit.prevent="applyFilters" class="mb-6 flex flex-wrap gap-4">
          <div v-for="(options, name) in filters" :key="name" class="flex flex-col gap-1">
            <label class="text-sm font-medium capitalize">{{ name }}</label>
  
    <Multiselect
      v-model="selectedFilters[name]"
      :options="options"
      mode="multiple"
      :track-by="'id'"
      :label="'name'"
      :searchable="true"
      placeholder="Select..."
      :close-on-select="false"
      :clear-on-select="false"
      :hide-selected="false"
      :show-labels="false"
      :classes="{
        container: 'relative text-sm w-64',
        search: 'w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500',
        option: 'flex items-center gap-2 cursor-pointer text-sm px-3 py-2 hover:bg-gray-100',
        optionSelected: 'bg-indigo-50 text-indigo-700 font-medium',
        optionCheckbox: 'form-checkbox text-indigo-600 h-4 w-4',
        tags: 'flex flex-wrap gap-1 px-1 py-1 text-xs',
        tag: 'bg-indigo-100 text-indigo-700 rounded px-2 py-0.5',
      }"
      :custom-label="option => option.name ?? option"
    >
      <!-- ✅ Custom option template with checkbox -->
      <template #option="{ option, isSelected }">
        <div class="flex items-center gap-2">
          <input
            type="checkbox"
            :checked="isSelected"
            class="form-checkbox text-indigo-600 h-4 w-4"
            readonly
          />
          <span>{{ option.name ?? option }}</span>
        </div>
      </template>
    </Multiselect>
  </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
                <slot name="extraHeaders" />
                <th v-if="!readOnly" class="px-6 py-3 text-right text-sm font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
              <tr
                v-for="item in items"
                :key="item.id"
                :class="{ 'cursor-pointer hover:bg-gray-100': clickableRows }"
                @click="clickableRows ? goToShow(item) : null"
                @keydown.enter="clickableRows ? goToShow(item) : null"
                tabindex="0"
              >
                <td class="px-6 py-4">{{ item.name }}</td>
                <slot name="extraColumns" :item="item" />

                <td v-if="!readOnly" class="px-6 py-4 text-right space-x-2">
                  <button @click="$emit('edit', item)">Edit</button>
                  <button @click.prevent="$emit('delete', item)" class="text-red-600 hover:text-red-800 font-medium">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Multiselect from '@vueform/multiselect'


const props = defineProps({
  title: { type: String, required: true },
  routeBase: { type: String, required: true },
  items: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  clickableRows: { type: Boolean, default: false },
  showCreate: { type: Boolean, default: true },
  readOnly: { type: Boolean, default: false },
})

const selectedFilters = ref({})

const singularTitle = computed(() =>
  props.title.endsWith('s') ? props.title.slice(0, -1) : props.title
)

function applyFilters() {
  router.get(route(`${props.routeBase}.index`), { filters: selectedFilters.value }, { preserveState: true })
}

function goToShow(item) {
  router.visit(route(`${props.routeBase}.show`, item))
}

watch(selectedFilters, () => {
  router.get(route(`${props.routeBase}.index`), { filters: selectedFilters.value }, {
    preserveState: true,
    replace: true,
  })
}, { deep: true })
</script>

<style scoped>
.multiselect-container.multiselect--active .multiselect-dropdown {
  display: block !important;
}
</style>
