<script setup>
import { useForm, Link} from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Multiselect from '@vueform/multiselect'

const props = defineProps({
  ticket: {
    type: Object,
    default: () => ({
      title: '',
      user: null,
      description: '',
      priority: null,
      status: null,
      categories: [],
      labels: [],
    })
  },
  statuses: Array,
  priorities: Array,
  categories: Array,
  labels: Array,
  users: Array,
})

console.log('Form props:', {
  statuses: props.statuses,
  priorities: props.priorities,
  users: props.users,
  categories: props.categories,
  labels: props.labels
})


const form = useForm({
  title: props.ticket.title || '',
  description: props.ticket.description || '',
  priority_id: props.ticket.priority?.id || null,
  status_id: props.ticket.status?.id || null,
  user_id: props.ticket.user?.id || null,
  category_ids: props.ticket.categories?.map(c => c.id) || [],
  label_ids: props.ticket.labels?.map(l => l.id) || [],
})

const isEditing = !!props.ticket?.id

function submit() {
  if (isEditing) {
    form.put(route('tickets.update', props.ticket.id))
  } else {
    form.post(route('tickets.store'))
  }
}
</script>

<template>
  <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content (Left Side) -->
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Title</label>
          <input 
            v-model="form.title" 
            type="text" 
            class="w-full border-gray-300 rounded-md" 
            required 
          />
          <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea 
            v-model="form.description" 
            rows="8"
            class="w-full border-gray-300 rounded-md"
            placeholder="Describe the ticket..."
          ></textarea>
          <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
          <p class="text-red-500 text-sm mt-2">ADD THE ATTACHMENTS</p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2">
        <button 
          type="submit" 
          :disabled="form.processing"
          class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 disabled:opacity-50"
        >
          {{ isEditing ? 'Update' : 'Create' }}
        </button>
        <Link 
          :href="route('tickets.index')" 
          class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Cancel
        </Link>
      </div>
    </div>

    <!-- Sidebar (Right Side) -->
    <div class="space-y-4">
      <!-- Assignee -->
      <div class="bg-white rounded-lg shadow p-4">
        <label class="block text-sm font-medium mb-2">Assignee</label>
        <select 
          v-model="form.user_id"
          class="w-full border-gray-300 rounded-md"
        >
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
        <div v-if="form.errors.user_id" class="text-red-600 text-sm mt-1">{{ form.errors.user_id }}</div>
      </div>

      <!-- Priority -->
      <div class="bg-white rounded-lg shadow p-4">
        <label class="block text-sm font-medium mb-2">Priority</label>
        <select 
          v-model="form.priority_id"
          class="w-full border-gray-300 rounded-md"
        >
          <option v-for="priority in priorities" :key="priority" :value="priority.id">
            {{ priority.name}}
          </option>
        </select>
        <div v-if="form.errors.priority_id" class="text-red-600 text-sm mt-1">{{ form.errors.priority_id }}</div>
      </div>

      <!-- Status -->
      <div class="bg-white rounded-lg shadow p-4">
        <label class="block text-sm font-medium mb-2">Status</label>
        <select 
          v-model="form.status_id"
          class="w-full border-gray-300 rounded-md"
        >
          <option v-for="status in statuses" :key="status" :value="status.id">
            {{ status.name}}
          </option>
        </select>
        <div v-if="form.errors.status_id" class="text-red-600 text-sm mt-1">{{ form.errors.status_id }}</div>
      </div>

      <!-- Categories -->
      <div class="bg-white rounded-lg shadow p-4">
        <label class="block text-sm font-medium mb-2">Categories</label>
        <Multiselect
          v-model="form.category_ids"
          :options="categories"
          :value-prop="'id'"
          :track-by="'id'"
          mode="multiple"
          :label="'name'"
          :searchable="true"
          placeholder="Select categories..."
          :close-on-select="false"
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
              <span>{{ option.name }}</span>
            </div>
          </template>
        </Multiselect>
        <div v-if="form.errors.category_ids" class="text-red-600 text-sm mt-1">{{ form.errors.category_ids }}</div>
      </div>

      <!-- Labels -->
      <div class="bg-white rounded-lg shadow p-4">
        <label class="block text-sm font-medium mb-2">Labels</label>
        <Multiselect
          v-model="form.label_ids"
          :options="labels"
          :value-prop="'id'"
          :track-by="'id'"
          mode="multiple"
          :label="'name'"
          :searchable="true"
          placeholder="Select labels..."
          :close-on-select="false"
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
              <span>{{ option.name }}</span>
            </div>
          </template>
        </Multiselect>
        <div v-if="form.errors.label_ids" class="text-red-600 text-sm mt-1">{{ form.errors.label_ids }}</div>
      </div>
    </div>
  </form>
</template>