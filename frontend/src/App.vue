<template>
  <router-view />
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import api from '@/axios'

let intervalId = null

onMounted(() => {
  // Hacer ping cada 30 segundos
  intervalId = setInterval(async () => {
    try {
      await api.get('/ping')
    } catch (error) {
      console.error('Error al verificar conexión:', error)
    }
  }, 60000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>

<style>

</style>
