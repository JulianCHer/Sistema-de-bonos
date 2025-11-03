<template>
    <aside
      :class="[
        darkMode ? 'bg-gray-800 text-gray-200' : 'bg-gray-50 text-gray-800',
        collapsed ? 'w-20' : 'w-64',
        'h-screen flex flex-col shadow-md transition-all duration-300'
      ]"
    >

      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-2">
          <img src="../assets/imagotipo.png" alt="Logo" class="w-[70%]" />
          <span v-if="!collapsed" class="text-lg font-semibold"></span>
        </div>
        <button @click="$emit('toggleSidebar')" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
          <component :is="collapsed ? 'lucide-chevron-right' : 'lucide-chevron-left'" class="w-5 h-5" />
        </button>
      </div>
  
      <nav class="flex-1 overflow-y-auto p-3">
        <ul class="space-y-1">
          <li v-for="item in menu" :key="item.name">
            <div
              @click="toggleSubmenu(item)"
              class="flex items-center justify-between p-2 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700"
            >
              <div class="flex items-center space-x-3">
                <component :is="item.icon" class="w-5 h-5" />
                <span v-if="!collapsed">{{ item.name }}</span>
              </div>
              <component
                v-if="item.children && !collapsed"
                :is="item.open ? 'lucide-chevron-up' : 'lucide-chevron-down'"
                class="w-4 h-4"
              />
            </div>
  
            <ul v-if="item.children && item.open && !collapsed" class="ml-8 mt-1 space-y-1">
              <li
                v-for="sub in item.children"
                :key="sub.name"
                class="flex justify-between items-center p-2 pl-3 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
              >
                <span>{{ sub.name }}</span>
                <span
                  v-if="sub.badge"
                  :class="[
                    'text-xs px-2 py-0.5 rounded-full',
                    sub.badgeColor === 'green' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'
                  ]"
                >
                  {{ sub.badge }}
                </span>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
  
      <!-- Footer -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <button
          @click="$emit('toggleTheme')"
          class="flex items-center space-x-2 p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
        >
          <component :is="darkMode ? 'lucide-sun' : 'lucide-moon'" class="w-5 h-5" />
          <span v-if="!collapsed">{{ darkMode ? 'Modo claro' : 'Modo oscuro' }}</span>
        </button>
      </div>
    </aside>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import {
    LayoutGrid,
    Box,
    ShoppingBag,
    Wallet,
    ChevronDown,
    ChevronUp,
    ChevronLeft,
    ChevronRight,
    Sun,
    Moon
  } from 'lucide-vue-next'
  
  defineProps({
    collapsed: Boolean,
    darkMode: Boolean
  })
  
  const menu = ref([
    { name: 'Dashboard', icon: LayoutGrid },
    {
      name: 'Product',
      icon: Box,
      open: false,
      children: [
        { name: 'Drafts', badge: 2, badgeColor: 'red' },
        { name: 'Released' },
        { name: 'Comments' },
        { name: 'Scheduled', badge: 8, badgeColor: 'green' }
      ]
    },
    { name: 'Purchases', icon: ShoppingBag },
    { name: 'Wallet', icon: Wallet }
  ])
  
  const toggleSubmenu = (item) => {
    if (item.children) item.open = !item.open
  }
  </script>
  