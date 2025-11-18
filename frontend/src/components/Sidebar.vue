<template>
  <aside
    :class="[
      darkMode ? 'bg-gray-800 text-gray-200' : 'bg-gray-50 text-gray-800',
      'h-screen flex flex-col shadow-md transition-all duration-300'
    ]"
  >

    <div class="flex items-center justify-between w-full p-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center space-x-2 w-full">
        <img
          :src="logo"
          alt="Logo"
          class="logo transition-all duration-500 w-[50%]"
        />
        <span class="text-lg font-semibold"></span>
      </div>
    </div>

    <!-- Menú -->
    <nav class="flex-1 overflow-y-auto p-3">
  <ul class="space-y-1">
    <li
      v-for="item in menu"
      :key="item.name"
      @click="goTo(item)"
      class="flex items-center justify-between p-2 rounded-lg cursor-pointer transition-all duration-300"
      :class="[
        isActive(item.route)
          ? 'bg-[#283F69] text-white shadow-md'
          : 'hover:bg-[#283F69] hover:text-white'
      ]"
    >
      <div class="flex items-center space-x-3">
        <component :is="item.icon" class="w-5 h-5" />
        <span>{{ item.name }}</span>
      </div>
    </li>
  </ul>
</nav>

    <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between h-[10%]">
      <div
        @click="$emit('toggleTheme')"
        class="relative flex items-center justify-between w-[40%] h-[80%] pl-[2%] rounded-full cursor-pointer overflow-hidden transition-all duration-500"
        :class="[
          darkMode ? 'bg-black text-white' : 'bg-gray-300 text-black',
        ]"
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
        class="flex items-center rounded text-red-600 cursor-pointer text-[12px]"
      >
        <LogOut/>
        <span>Cerrar sesión</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Sun as SunIcon, Moon as MoonIcon, Ticket, FileBadge, File, Users, LogOut } from 'lucide-vue-next'
import logo from '../assets/imagotipo.png'

const props = defineProps({
  darkMode: Boolean
})

const router = useRouter()
const route = useRoute()
const menu = ref([])
const isActive = (path) => route.path === path

const goTo = (item) => {
  if (item.route) {
    router.push(item.route)
  }
}

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
  const idGroup = JSON.parse(localStorage.getItem('id_group'))

  if (idGroup === 1) {
    menu.value = [
      { name: 'SORTEOS', icon: FileBadge, route: '/sorteo' },
      { name: 'BONOS', icon: Ticket, route: '/bonos' },
      { name: 'VENDEDORES', icon: Users, route: '/vendedores' },
      { name: 'USUARIOS', icon: Users, route: '/usuarios' },
      { name: 'REPORTES', icon: File, route: '/reportes' }
    ]
  } else if (idGroup === 2) {
    menu.value = [
      { name: 'BONOS', icon: Ticket, route: '/bonos' },
      { name: 'REPORTES', icon: File, route: '/reportes' },
      { name: 'SORTEOS', icon: FileBadge, route: '/sorteos' }
    ]
  } else {
    menu.value = [
      { name: 'SORTEOS', icon: FileBadge, route: '/sorteos' },
      { name: 'BONOS', icon: Ticket, route: '/bonos' }
    ]
  }
})
</script>
