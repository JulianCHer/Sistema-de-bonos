<template>
    <div class="flex flex-col items-center justify-center h-screen text-center bg-gray-100 dark:bg-gray-900">
        <div ref="lottieContainer" class="w-full h-[350px]"></div>
        <p class="text-[24px] font-semibold text-gray-600 dark:text-gray-300 mb-6">
            La página que intentas acceder no hace parte del sistema.
        </p>
    </div>
</template>

<script setup>  
import { onMounted, ref } from 'vue'
import lottie from 'lottie-web'
import animationData from '@/assets/NotFound.json' // si la descargaste

const lottieContainer = ref(null)

onMounted(() => {
    const anim = lottie.loadAnimation({
        container: lottieContainer.value,
        renderer: 'svg',
        loop: false, // ❌ No se repite
        autoplay: true,
        animationData
    })
})
</script>