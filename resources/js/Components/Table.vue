<template>
  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
       <div v-if="showCreateButton" class="mb-4">
      <CreateButton 
        :title="title"
        @click="emit('open-create-modal')" 
      />
    </div>
      <div class="bg-white shadow sm:rounded-lg p-6 text-gray-900">
       <form @submit.prevent="applyFilters" class="mb-6 flex flex-wrap gap-4">
          <div v-for="(options, name) in filters" :key="name" class="flex flex-col gap-1">
            <label class="text-sm font-medium capitalize">{{ name }}</label>
  
    <Multiselect
      v-model="selectedFilters[name]"
      :options="options"
      :value-prop="'id'"
      :track-by="'id'"
      mode="multiple"
      :label="'name'"
      :searchable="true"
      placeholder="Select..."
      :reset-on-options-change="true"
      :close-on-select="false"
      :clear-on-select="false"
      :hide-selected="false"
      :show-labels="false"
      :classes="{
    container: 'relative text-sm w-64',
    containerActive: 'ring ring-indigo-200 border-indigo-500',
    multiselect: 'border border-gray-300 rounded-md bg-white min-h-[38px]',
    multiselectOpen: 'border-indigo-500',
    search: 'w-full px-3 py-2 text-sm focus:outline-none',
    caret: 'absolute right-3 pointer-events-none',
    clear: 'absolute right-8 flex items-center justify-center',
    option: 'flex items-center gap-2 cursor-pointer text-sm px-3 py-2 hover:bg-gray-100',
    optionSelected: 'bg-indigo-50 text-indigo-700 font-medium',
    optionCheckbox: 'form-checkbox text-indigo-600 h-4 w-4',
    tags: 'flex flex-wrap gap-1 px-1 py-1 text-xs',
    tag: 'bg-indigo-100 text-indigo-700 rounded px-2 py-0.5',
      }"
    >
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
                v-for="item in items.data"
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
           <div v-if="items && items.links" class="pagination mt-4 flex gap-2 flex-wrap">
  <template v-for="link in items.links" :key="link.label">
    <Link
      v-if="link.url"
      :href="link.url"
      v-html="link.label"
      preserve-state
      preserve-scroll
      class="px-3 py-1 border rounded hover:bg-gray-100"
    />
    <span
      v-else
      v-html="link.label"
      class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed"
    />
  </template>
</div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Multiselect from '@vueform/multiselect'
import CreateButton from './CreateButton.vue'


const props = defineProps({
  items: { type: Object, default: () => ({ data: [], links: [] }) },
  title: { type: String, required: true },
  routeBase: { type: String, required: true },
  filters: Object,     
  appliedFilters: { type: Object, default: () => ({}) },
  clickableRows: { type: Boolean, default: false },
  showCreateButton: { type: Boolean, default: true },
  readOnly: { type: Boolean, default: false },
  onCreateClick: {
    type: Function,
    default: null
  }
})

const emit = defineEmits(['edit', 'delete', 'open-create-modal'])

const selectedFilters = ref({})

onMounted(async() => {
 await nextTick()

  const sel = {}

  for (const key in props.filters) {
    const options = props.filters[key] || []
    sel[key] = []

    const applied = props.appliedFilters?.[key] || []

    sel[key] = options.filter(option => {
      if (!option) return false

      if (typeof option === 'object') {
        return applied.some(a => (a.id && a.id === option.id) || (a.name && a.name === option.name))
      } else {
        return applied.includes(option)
      }
    })
  }

  selectedFilters.value = sel
})


const cleanFilters = computed(() => {
const filters = {};
  for (const [key, value] of Object.entries(selectedFilters.value)) {
    if (!value || value.length === 0) continue;

    if (value.length && typeof value[0] === 'object' && 'name' in value[0]) {
      filters[key] = value.map(v => v.name);
    } else {
      filters[key] = value;
    }
  }
  return filters;
})

function applyFilters() {
  router.get(route(`${props.routeBase}.index`), { filters: cleanFilters.value }, {
    preserveState: true,
    replace: true,
  })
}

watch(selectedFilters, () => {
  applyFilters()
}, { deep: true })

const singularTitle = computed(() => {
  if (props.title.endsWith('ies')) {
    return props.title.slice(0, -3) + 'y'
  }
  return props.title.endsWith('s') ? props.title.slice(0, -1) : props.title
})

function goToShow(item) {
  router.visit(route(`${props.routeBase}.show`, item))
}

</script>

<style scoped>
.multiselect-container.multiselect--active .multiselect-dropdown {
  display: block !important;
}
</style>
