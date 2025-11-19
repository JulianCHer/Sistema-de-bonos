<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axios'
import imagotipo from '@/assets/user.png'

defineProps({
  darkMode: Boolean
})

const fetchedUser = ref(null)
const loadingUser = ref(true)

const greetingName = computed(() => {
  if (!fetchedUser.value) return 'Usuario'
  const parts = [fetchedUser.value.name, fetchedUser.value.surname].filter(Boolean)
  return parts.length ? parts.join(' ') : fetchedUser.value.email || 'Usuario'
})

const topBarAvatar = computed(() => fetchedUser.value?.img_url || imagotipo)

const loadUser = async () => {
  try {
    const response = await api.get('/users', { params: { per_page: 1 } })
    fetchedUser.value = response.data?.users?.[0] || null
  } catch (error) {
    console.error('No se pudo cargar el usuario para el top bar', error)
  } finally {
    loadingUser.value = false
  }
}

onMounted(loadUser)
</script>

<template>
  <div
    class="top_bar flex items-center justify-between px-[1%] text-white border-b"
    :class="[
      darkMode
        ? 'bg-[#1f2937] border-b-white/30'
        : 'bg-white border-b-[#283f69]/30'
    ]"
  >
    <div class="top_bar_content flex flex-col flex-1">
      <p
        class="top_bar_greeting text-[20px] font-bold transition-colors"
        :class="[darkMode ? 'text-[#f3f6ff]' : 'text-[#283F69]']"
      >
        {{ loadingUser ? 'Cargando usuario...' : `Hola soy, ${greetingName}` }}
      </p>
    </div>
    <div
      class="top_bar_avatar inline-flex overflow-hidden rounded-full transition-all duration-200 p-[5px]">
      <img
        :src="topBarAvatar"
        alt="Avatar del usuario"
        class="w-16 h-16 rounded-full object-cover"
      />
    </div>
  </div>
</template>
