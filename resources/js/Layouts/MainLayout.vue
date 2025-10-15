<template>
  <div>
    <nav class="bg-white border-b border-gray-100" :class="{ 'overflow-hidden': open }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="shrink-0 flex items-center">
              <AppLogo />
            </div>

            <!-- Navigation Links -->
            <div class="hidden sm:flex items-center space-x-6 ml-4 mr-3">
              <Link :href="'/tickets'" class="nav-link" :class="{ 'active': $page.url.startsWith('/tickets') }">Tickets</Link>

              <template v-if="user?.role?.name === 'admin'">
                <Link :href="'/dashboard'" class="nav-link" :class="{ 'active': $page.url === '/dashboard' }">Dashboard</Link>
                <Link :href="'/ticket-logs'" class="nav-link" :class="{ 'active': $page.url.startsWith('/ticket-logs') }">Ticket Logs</Link>
                <Link :href="'/users'" class="nav-link" :class="{ 'active': $page.url.startsWith('/users') }">Users</Link>
                <Link :href="'/categories'" class="nav-link" :class="{ 'active': $page.url.startsWith('/categories') }">Categories</Link>
                <Link :href="'/labels'" class="nav-link" :class="{ 'active': $page.url.startsWith('/labels') }">Labels</Link>
              </template>
            </div>
          </div>

          <!-- Settings Dropdown -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="relative">
              <button 
               @click="dropdown = !dropdown"
              class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                <div v-if="user">{{ user.name }}</div>
                <div class="ml-1">
                  <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0L5.293 8.707a1 1 0 010-1.414z"/>
                  </svg>
                </div>
              </button>

              <div v-if="dropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-md z-50">
                <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</Link>
                <Link method="post" href="/logout" as="button"
                      class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Log Out
                </Link>
              </div>
            </div>
          </div>

          <!-- Hamburger -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main>
      <slot>
        <HeadTitle />
      </slot>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { NavigationMenuLink } from 'radix-vue'
import AppLogo  from '@/Components/AppLogo.vue'
import { Ticket } from 'lucide-vue-next'
import HeadTitle from '../Components/HeadTitle.vue'

const page = usePage()
console.log('Inertia page props:', page.props)
const user = computed(() => page.props.auth.user)


const open = ref(false)
const dropdown = ref(false)
</script>

<style scoped>
.nav-link {
  display: inline-block;
  padding: 0.5rem 2rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #4B5563;
  border-bottom: 2px solid transparent;
  transition: border-color 0.2s;
}
.nav-link.active {
  border-color: #6366F1;
  color: #1F2937;
}

.responsive-nav-link {
  display: block;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: 500;
  color: #4B5563;
  text-decoration: none;
}
.responsive-nav-link.active {
  background-color: #EDF2F7;
  color: #1F2937;
}
</style>
