<script setup>
import { ref, onMounted, watch } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import mainsorteo from '@/components/Mainsorteo.vue'

const darkMode = ref(false)

const toggleTheme = () => (darkMode.value = !darkMode.value)

onMounted(() => {
    const savedMode = localStorage.getItem('darkmode')
    if (savedMode === '1') darkMode.value = true
    else if (savedMode === '0') darkMode.value = false
    else localStorage.setItem('darkmode', darkMode.value ? '1' : '0')

    document.documentElement.classList.toggle('dark', darkMode.value)
})

watch(darkMode, (newVal) => {
    document.documentElement.classList.toggle('dark', newVal)
    localStorage.setItem('darkmode', newVal ? '1' : '0')
})
</script>

<template>
    <div :class="[
        darkMode ? 'dark bg-gray-900 text-white' : 'bg-white text-gray-900',
        ]" 
        class="flex h-screen w-full">
        <Sidebar class="w-[15%]" :darkMode="darkMode" @toggleTheme="toggleTheme"
             />

        <mainsorteo class="flex-1 p-6 transition-all duration-300 overflow-y-auto w-[85%]" :darkMode="darkMode"/>
    </div>
</template>
