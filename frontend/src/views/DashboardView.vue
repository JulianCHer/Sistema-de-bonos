<script setup>
import { ref, onMounted, watch } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const collapsed = ref(false)
const darkMode = ref(false)

const toggleSidebar = () => (collapsed.value = !collapsed.value)

const toggleTheme = () => {
  darkMode.value = !darkMode.value
}

// 🔹 Al montar, leer tema guardado o preferencia del sistema
onMounted(() => {
  const savedTheme = localStorage.getItem('theme')

  if (savedTheme) {
    darkMode.value = savedTheme === 'dark'
  } else {
    darkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }

  document.documentElement.classList.toggle('dark', darkMode.value)
})

// 🔹 Guardar y aplicar clase automáticamente
watch(darkMode, (newVal) => {
  document.documentElement.classList.toggle('dark', newVal)
  localStorage.setItem('theme', newVal ? 'dark' : 'light')
})
</script>

<template>
    <div :class="darkMode ? 'dark bg-gray-600 text-white' : 'bg-white text-gray-900'" class="flex h-screen">
        <Sidebar
            :collapsed="collapsed"
            :darkMode="darkMode"
            @toggleSidebar="toggleSidebar"
            @toggleTheme="toggleTheme"
        />

        <main class="flex-1 p-6 transition-all duration-300 overflow-y-auto">
            <router-view />
        </main>
    </div>
</template>
