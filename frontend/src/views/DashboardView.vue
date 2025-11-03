<script setup>
import { ref } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const collapsed = ref(false)
const darkMode = ref(false)

const toggleSidebar = () => (collapsed.value = !collapsed.value)
const toggleTheme = () => (darkMode.value = !darkMode.value)
</script>

<template>
    <div :class="darkMode ? 'dark bg-gray-900 text-white' : 'bg-gray-100 text-gray-900'" class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar :collapsed="collapsed" :dark-mode="darkMode" @toggleSidebar="toggleSidebar"
            @toggleTheme="toggleTheme" />

        <!-- Main Content -->
        <main class="flex-1 p-6 transition-all duration-300 overflow-y-auto">
            <router-view />
        </main>
    </div>
</template>
