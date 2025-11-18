<template>
    <div class="total_section w-[85%] py-[2%] flex flex-col align-items-center">
        <!-- 🔹 Botón Nuevo sorteo -->
        <div class="button_section flex justify-end p-[2%] w-[100%] mb-4">
            <button class="border border-[#283F69] px-4 py-2 rounded
            transition-all duration-300 ease-in-out cursor-pointer" @click="showModal = true" :class="[
                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
            ]">
                Nuevo Sorteo
            </button>
        </div>
        <hr class="w-[100%] text-center opacity-10 py-[1%]" />

        <!-- 🔹 Listado de sorteos -->
        <div class="sorteo px-[2%]">
            <h1 class="text-[30px] uppercase font-bold" style="margin-bottom: 2%;" :class="[
                darkMode ? 'text-white' : 'text-[#283F69]',
            ]">
                Listado de sorteos activos
            </h1>

            <div v-if="cargando" class="text-gray-500 text-center py-10 text-lg">
                Cargando sorteos...
            </div>

            <div v-else-if="sorteos.length === 0" class="text-center py-10 text-gray-500 text-lg" :class="[
                darkMode ? 'text-white' : 'text-[#283F69]',
            ]">
                No hay sorteos registrados
            </div>

            <div v-else class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div v-for="(sorteo, index) in sorteos" :key="sorteo.Id" class="relative bg-white rounded-xl shadow-md p-4 border border-gray-200 
         transition-transform duration-300 ease-in-out transform hover:scale-105 hover:shadow-xl 
         cursor-pointer">
                    <div class="absolute -top-3 -left-3 bg-[#283F69] text-white font-bold px-3 py-1 rounded-lg shadow">
                        {{ index + 1 }}
                    </div>

                    <div class="text-center">
                        <h2 class="text-xl !font-bold uppercase text-[#283F69] mb-2">
                            {{ sorteo.Nombre }}
                        </h2>

                        <p class="text-gray-700 mb-2">
                            <strong class="!font-bold">Fecha Premio:</strong> {{ sorteo.Fecha }}
                        </p>

                        <p class="text-gray-700 mb-2">
                            <strong class="!font-bold">Fecha Registro:</strong> {{ sorteo.Fecha_Registro }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- 🔹 Fondo oscuro translúcido -->
            <div class="absolute inset-0 bg-black" style="opacity: 60%;"></div>

            <!-- 🔹 Contenedor del modal -->
            <div class="relative bg-white dark:bg-gray-800 p-[2%] rounded-lg w-[90%] md:w-[40%] shadow-lg z-10">
                <h2 class="text-2xl text-center text-white bg-[#283F69] p-[1%] rounded-t-[10px]"
                    style="margin-bottom: 2%;">
                    Crear nuevo sorteo
                </h2>

                <form @submit.prevent="crearSorteo">
                    <div class="mb-3">
                        <label class="block font-medium mb-1" :class="[
                            darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                        ]">Nombre del sorteo:</label>
                        <input type="text" required
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#283F69]"
                            :class="[
                                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                            ]" placeholder="Nombre del sorteo" />
                    </div>

                    <div class="mb-3">
                        <label class="block font-medium mb-1" :class="[
                            darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                        ]">¿Tendrá subsorteos?</label>
                        <select
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#283F69]"
                            :class="[
                                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                            ]">
                            <option value="0" :selected="0">No</option>
                            <option value="1">Si</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block font-medium mb-1" :class="[
                            darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                        ]">Fecha del sorteo:</label>
                        <input type="date" required
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#283F69]"
                            :class="[
                                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                            ]" />
                    </div>

                    <div class="mb-3">
                        <label class="block font-medium mb-1" :class="[
                            darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                        ]">Total de tickets:</label>
                        <input type="number" min="1" value="10000" required
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#283F69]"
                            :class="[
                                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                            ]" />
                    </div>

                    <div class="flex justify-around w-[30%]" style="margin-top: 2%;">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 rounded border hover:bg-gray-200 transition-all duration-300" :class="[
                                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                            ]">
                            Cerrar
                        </button>

                        <button type="submit"
                            class="px-4 py-2 rounded bg-[#283F69] text-white hover:bg-white hover:text-[#283F69] border border-[#283F69] transition-all duration-300">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>



</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'

const props = defineProps({
    darkMode: Boolean
})

const sorteos = ref([])
const showModal = ref(false)
const cargando = ref(false)
const nuevoSorteo = ref({
    Nombre: '',
    Sorteo_padre: null,
    Fecha: '',
    Total_Tickets: 0
})

// =====================
// Cargar sorteos
// =====================
const getSorteos = async () => {
    try {
        cargando.value = true
        const response = await api.get('/sorteos')
        sorteos.value = response.data.sorteos || []
    } catch (error) {
        console.error('Error al cargar sorteos:', error)
    } finally {
        cargando.value = false
    }
}

// =====================
// Crear nuevo sorteo
// =====================
const crearSorteo = async () => {
    try {
        const response = await api.post('/sorteos', nuevoSorteo.value)
        if (response.data.success) {
            showModal.value = false
            await getSorteos()
            nuevoSorteo.value = { Nombre: '', Sorteo_padre: null, Fecha: '', Total_Tickets: 0 }
        } else {
            alert('No se pudo crear el sorteo.')
        }
    } catch (error) {
        console.error('Error al crear sorteo:', error)
        alert('Error al crear el sorteo.')
    }
}

onMounted(() => {
    getSorteos()
})
</script>