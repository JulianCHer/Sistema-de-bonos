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
              class="flex items-center justify-between p-2 rounded-lg cursor-pointer hover:bg-[#283F69] hover:text-white"
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
  
      <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between h-[10%]">
        <div
          @click="toggleMode"
          class="relative flex items-center justify-between w-[40%] h-[80%] pl-[2%] rounded-full cursor-pointer overflow-hidden transition-all duration-500"
          :class="darkMode ? 'bg-black text-white' : 'bg-gray-300 text-black'"
        >
          <div
            class="absolute w-[30%] h-[70%] rounded-full flex items-center justify-center shadow-md transition-all duration-500"
            :class="darkMode ? 'translate-x-13 bg-white text-black' : 'translate-x-0 bg-white text-yellow-500'"
          >
            <component :is="darkMode ? MoonIcon : SunIcon" class="w-[80%] h-[80%] transition-all duration-500" />
          </div>
        </div>
        <button
          @click="logout"
          class="flex items-center rounded text-red-600 cursor-pointer"
        >
          <LogOut/>
          <span v-if="!collapsed">Cerrar sesión</span>
        </button>
      </div>
    </aside>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { Sun as SunIcon, Moon as MoonIcon } from 'lucide-vue-next'
  import {
    Ticket,
    FileBadge,
    File,
    Users,
    LogOut
  } from 'lucide-vue-next'

  const router = useRouter()
  const darkMode = ref(false)
  
  defineProps({
    collapsed: Boolean,
    darkMode: Boolean
  })

  const toggleMode = () => {
    darkMode.value = !darkMode.value
    document.documentElement.classList.toggle('dark', darkMode.value)
    localStorage.setItem('darkmode', darkMode.value ? '1' : '0')
  }
  
  const menu = ref([
    { name: 'SORTEOS', icon: FileBadge },
    { name: 'BONOS', icon: Ticket},
    { name: 'USUARIOS', icon: Users },
    { name: 'REPORTES', icon: File }
  ])
  
  const toggleSubmenu = (item) => {
    if (item.children) item.open = !item.open
  }

  const logout = () => {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('id_group')
    router.push('/')
  }

  onMounted(() => {
    const savedDark = localStorage.getItem('darkmode')
    if (savedDark === '1') {
      darkMode.value = true
      document.documentElement.classList.add('dark')
    } else {
      darkMode.value = false
      document.documentElement.classList.remove('dark')
    }
    const idGroup = JSON.parse(localStorage.getItem('id_group'))

    if (idGroup === 1) {
      // 🧑‍💼 Grupo 1: administrador
      menu.value = [
        { name: 'SORTEOS', icon: FileBadge },
        { name: 'BONOS', icon: Ticket },
        { name: 'VENDEDORES', icon: Users },
        { name: 'USUARIOS', icon: Users },
        { name: 'REPORTES', icon: File }
      ]
    } else if (idGroup === 2) {
      // 👥 Grupo 2: vendedor u otro rol
      menu.value = [
        { name: 'BONOS', icon: Ticket },
        { name: 'REPORTES', icon: File },
        { name: 'SORTEOS', icon: FileBadge }
      ]
    } else {
      // Si no hay grupo o es inválido, mostrar un menú por defecto
      menu.value = [
        { name: 'SORTEOS', icon: FileBadge },
        { name: 'BONOS', icon: Ticket }
      ]
    }
  })
  </script>
  