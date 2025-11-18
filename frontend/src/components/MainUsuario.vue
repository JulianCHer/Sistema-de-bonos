<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import api from '@/axios'
import Swal from 'sweetalert2'
import { SquarePen, Trash2, Camera } from 'lucide-vue-next'
import imagotipo from '@/assets/user.png'
import loading from '@/assets/loading.json'
import lottie from 'lottie-web'

const props = defineProps({
    darkMode: Boolean
})

const users = ref([])
const New_User = ref(false)
const cargando = ref(false)
const guardando = ref(false)
const editando = ref(false)
const previewImage = ref(null)
const loaderContainer = ref(null)
let loaderAnimation = null
const selectedUserId = ref(null)
const deletedUsers = ref([])
const showDeletedModal = ref(false)
const loadingDeleted = ref(false)
const currentPage = ref(1)
const perPage = ref(10)
const perPageOptions = [5, 10, 20, 50]
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0
})


const newUser = ref({
    nombre: '',
    apellido: '',
    email: '',
    password: '',
    telefono: '',
    tipo_documento: '',
    documento: '',
    grupo: '2',
    direccion: '',
    avatar: null
})

watch(
    () => guardando.value || cargando.value,
    async (isLoading) => {
        if (!isLoading) {
            if (loaderAnimation) {
                loaderAnimation.destroy()
                loaderAnimation = null
            }
            return
        }

        await nextTick()

        if (loaderContainer.value) {
            if (loaderAnimation) {
                loaderAnimation.destroy()
            }
            loaderAnimation = lottie.loadAnimation({
                container: loaderContainer.value,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                animationData: loading
            })
        }
    }
)

const getUsers = async (page = currentPage.value) => {
    try {
        cargando.value = true
        const response = await api.get('/users', {
            params: {
                page,
                per_page: perPage.value
            }
        })
        users.value = response.data.users || []
        if (response.data.pagination) {
            pagination.value = response.data.pagination
            currentPage.value = response.data.pagination.current_page || page
        } else {
            pagination.value = {
                current_page: page,
                last_page: 1,
                per_page: perPage.value,
                total: users.value.length
            }
            currentPage.value = page
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error)
        Swal.fire('Error', 'No se pudieron cargar los usuarios.', 'error')
    } finally {
        cargando.value = false
    }
}

const getDeletedUsers = async () => {
    try {
        loadingDeleted.value = true
        const response = await api.get('/users/erased_users')
        deletedUsers.value = response.data.users || []
    } catch (error) {
        console.error('Error al cargar usuarios eliminados:', error)
    } finally {
        loadingDeleted.value = false
    }
}

const abrirModalEliminados = async () => {
    showDeletedModal.value = true
    await getDeletedUsers()
}

const handlePerPageChange = () => {
    getUsers(1)
}

const goToPage = (page) => {
    if (page < 1 || page > (pagination.value.last_page || 1)) return
    getUsers(page)
}

const nextPage = () => {
    goToPage((currentPage.value || 1) + 1)
}

const prevPage = () => {
    goToPage((currentPage.value || 1) - 1)
}

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        newUser.value.avatar = file
        previewImage.value = URL.createObjectURL(file)
    }
}

const resetForm = () => {
    newUser.value = {
        nombre: '',
        apellido: '',
        email: '',
        password: '',
        telefono: '',
        tipo_documento: '',
        documento: '',
        grupo: '2',
        direccion: '',
        avatar: null
    }
    previewImage.value = null
    editando.value = false
    selectedUserId.value = null
}

const saveUser = async () => {
    const formData = new FormData()
    //Recorro los campos del formulario para agregarlos al formData
    for (const key in newUser.value) {
        const value = newUser.value[key]
        if (key === 'avatar' && !value) continue
        if (key === 'password' && editando.value && !value) continue
        formData.append(key, value ?? '')
    }


    try {
        //Me muestra el cargando
        guardando.value = true

        if (editando.value && selectedUserId.value) {
            await api.post(`/users/update/${selectedUserId.value}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            Swal.fire('Actualizado', 'El usuario fue actualizado correctamente.', 'success')
        } else {
            await api.post('/users/store', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            Swal.fire('Éxito', 'Usuario creado correctamente.', 'success')
        }

        New_User.value = false
        getUsers()
    } catch (error) {
        console.error('Error al guardar usuario:', error)
        Swal.fire('Error', 'No se pudo guardar el usuario.', 'error')
    } finally {
        guardando.value = false
        resetForm()
    }
}

const eliminarUsuario = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    })

    if (!confirm.isConfirmed) return

    try {
        await api.delete(`/users/${id}`)
        Swal.fire('Eliminado', 'El usuario ha sido eliminado correctamente.', 'success')
        await getUsers(currentPage.value)
    } catch (error) {
        console.error('Error al eliminar usuario:', error)
        Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error')
    }
}

const restaurarUsuario = async (id) => {
    const confirm = await Swal.fire({
        title: '¿Restaurar usuario?',
        text: 'El usuario volverá a estar activo',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    })

    if (!confirm.isConfirmed) return

    try {
        guardando.value = true
        await api.post(`/users/restore_users/${id}`)
        Swal.fire('Restaurado', 'El usuario ha sido restaurado correctamente.', 'success')
        await getUsers()
        await getDeletedUsers()
        if (deletedUsers.value.length === 0) {
            showDeletedModal.value = false
        }
    } catch (error) {
        console.error('Error al restaurar usuario:', error)
        Swal.fire('Error', 'No se pudo restaurar el usuario.', 'error')
    } finally {
        guardando.value = false
    }
}

const editarUsuario = (user) => {
    editando.value = true
    selectedUserId.value = user.id
    New_User.value = true

    // Cargar datos en el formulario
    newUser.value = {
        nombre: user.name,
        apellido: user.surname || '',
        email: user.email,
        password: '', // no se muestra por seguridad
        telefono: user.phone || '',
        tipo_documento: user.type_document || '',
        documento: user.document || '',
        grupo: user.id_group || '2',
        direccion: user.address || '',
        avatar: null
    }
    console.log(user.img_url);
    previewImage.value = user.img_url ? `http://127.0.0.1:8000/${user.img_url}` : imagotipo
}

onMounted(() => getUsers())

</script>

<template>
    <transition name="fade">
        <div v-if="guardando || cargando" class="fixed inset-0 flex items-center justify-center bg-white z-[9999]">
            <div ref="loaderContainer" class="w-[200px] h-[200px]"></div>
        </div>
    </transition>
    <transition name="fade">
        <div v-if="New_User" class="fixed inset-0 bg-[rgba(0,0,0,0.8)] flex items-center justify-center z-50 p-[2%]"
        @click.self="New_User = false; resetForm()">

            <div class="bg-white dark:bg-gray-800 rounded-2xl w-[90%] md:w-[60%] lg:w-[45%] shadow-2xl relative p-[2%]">
                <button @click="New_User = false; resetForm()"
                class="absolute top-6 right-6 text-gray-400 hover:text-red-500 text-[40px] font-bold">
                &times;
                </button>

                <!-- 🔹 Imagen -->
                <div class="flex flex-col items-center !mb-[2%]">
                    <div class="relative w-[120px] h-[120px]">
                        <img :src="previewImage || imagotipo" alt="Avatar"
                        class="rounded-full w-[120px] h-[120px] object-cover border-4 border-[#283F69]" />
                        <label for="avatarInput"
                        class="absolute bottom-0 right-0 bg-[#283F69] text-white rounded-full p-2 hover:scale-110 transition-transform cursor-pointer"
                        title="Subir nueva foto">
                        <Camera class="w-6 h-6" />
                        </label>
                        <input id="avatarInput" type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
                    </div>
                </div>

                <!-- 🔹 Título -->
                <div class="text-center !mb-[2%] text-white bg-[#283F69] p-[1%] rounded-t-[10px]">
                    <h1>{{ editando ? 'EDITAR USUARIO' : 'NUEVO USUARIO' }}</h1>
                </div>

                <!-- 🔹 Formulario -->
                <form @submit.prevent="saveUser" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left px-[5%]">
                    <div v-for="field in [
                        {label:'Nombre *', model:'nombre', type:'text', placeholder:'Nombre'},
                        {label:'Apellido *', model:'apellido', type:'text', placeholder:'Apellido'},
                        {label:'Email *', model:'email', type:'email', placeholder:'Correo electrónico'},
                        {label:'Contraseña *', model:'password', type:'password', placeholder:'Contraseña'}
                    ]" :key="field.model">
                        <label class="block text-sm font-semibold mb-1 text-[#283F69]">{{ field.label }}</label>
                        <input v-model="newUser[field.model]" :type="field.type" :placeholder="field.placeholder"
                        class="w-full border border-gray-300 text-black dark:border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#283F69]"
                        :required="!editando || field.model === 'password'" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-[#283F69]">Tipo de documento *</label>
                        <select v-model="newUser.tipo_documento"
                        class="w-full border border-gray-300 text-black dark:border-gray-600 rounded px-3 py-2 focus:ring-2 focus:ring-[#283F69] bg-transparent"
                        required>
                            <option value="">Seleccione</option>
                            <option value="CC">Cédula de ciudadanía</option>
                            <option value="CE">Cédula de extranjería</option>
                            <option value="TI">Tarjeta de identidad</option>
                            <option value="NIT">NIT</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-[#283F69]">Número de documento *</label>
                        <input v-model="newUser.documento" type="text" placeholder="Número de documento"
                        class="w-full border border-gray-300 text-black dark:border-gray-600 rounded px-3 py-2 focus:ring-2 focus:ring-[#283F69]"
                        required />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-[#283F69]">Grupo *</label>
                        <select v-model="newUser.grupo"
                        class="w-full border border-gray-300 text-black dark:border-gray-600 rounded px-3 py-2 focus:ring-2 focus:ring-[#283F69] bg-transparent">
                            <option value="1">Administrador</option>
                            <option value="2">Operador</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-[#283F69]">Teléfono</label>
                        <input v-model="newUser.telefono" type="text" placeholder="Número de teléfono"
                        class="w-full border border-gray-300 text-black dark:border-gray-600 rounded px-3 py-2 focus:ring-2 focus:ring-[#283F69]" />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-semibold mb-1 text-[#283F69]">Dirección</label>
                        <textarea v-model="newUser.direccion" placeholder="Dirección residencial" rows="2"
                        class="w-full border border-gray-300 text-black dark:border-gray-600 rounded px-3 py-2 focus:ring-2 focus:ring-[#283F69] resize-none"></textarea>
                    </div>

                    <div class="col-span-2 flex justify-end gap-4 mt-4">
                        <button type="button" @click="New_User = false; resetForm()"
                        class="px-5 py-2 border rounded text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                        Cancelar
                        </button>
                        <button type="submit"
                        class="px-5 py-2 bg-[#283F69] text-white rounded hover:bg-white hover:text-[#283F69] border border-[#283F69] transition-all">
                        {{ editando ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-if="showDeletedModal"
            class="fixed inset-0 bg-[rgba(0,0,0,0.8)] flex items-center justify-center z-50 p-[2%]"
            @click.self="showDeletedModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-2xl w-[90%] md:w-[70%] shadow-2xl relative p-[2%]">

                <h2 class="text-2xl font-bold !mb-[2%] text-center rounded-t-[10px] bg-[#283F69] p-[1%] text-white">
                    USUARIOS ELIMINADOS
                </h2>

                <div v-if="loadingDeleted" class="text-center text-gray-500 py-6">
                    Cargando usuarios eliminados...
                </div>

                <div v-else class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full border border-gray-200 bg-white text-gray">
                        <thead class="bg-[#283F69] text-white">
                            <tr>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">#</th>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">Nombre</th>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">Email</th>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">Teléfono</th>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">Documento</th>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">Grupo</th>
                                <th class="px-4 py-3 text-center text-[18px] !font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="deletedUsers.length === 0">
                                <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                    No hay usuarios eliminados.
                                </td>
                            </tr>
                            <tr v-else v-for="(user, index) in deletedUsers" :key="`deleted-${user.id}`"
                                :class="[darkMode ? 'bg-gray-800 hover:bg-gray-700' : 'bg-white hover:bg-gray-100']"
                                class="transition">
                                <td class="px-4 py-2 text-center">{{ index + 1 }}</td>
                                <td class="px-4 py-2 text-center">{{ user.name }} {{ user.surname }}</td>
                                <td class="px-4 py-2 text-center">{{ user.email }}</td>
                                <td class="px-4 py-2 text-center">{{ user.phone }}</td>
                                <td class="px-4 py-2 text-center">
                                    <strong class="uppercase">{{ user.type_document }} - </strong> {{ user.document }}
                                </td>
                                <td class="px-4 py-2 text-center">{{ user.group_name || 'Sin grupo' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button
                                        class="bg-green-600 text-white font-bold px-4 py-2 rounded hover:bg-green-700 transition"
                                        @click="restaurarUsuario(user.id)">
                                        Restaurar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </transition>

    <div class="total_section w-[85%] py-[2%] flex flex-col align-items-center">
        <!-- 🔹 Botón Nuevo usuario -->
        <div class="button_section flex flex-wrap gap-3 justify-end p-[2%] w-[100%] mb-4">
            <button class="border border-[#283F69] px-4 py-2 rounded
            transition-all duration-300 ease-in-out cursor-pointer" @click="New_User = true" :class="[
                darkMode ? 'bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
            ]">
                Nuevo Usuario
            </button>
            <button class="px-4 py-2 rounded bg-red-800 text-white transition-all duration-300 ease-in-out cursor-pointer"
                @click="abrirModalEliminados" >
                Usuarios eliminados
            </button>
        </div>
        <hr class="w-[100%] text-center opacity-10 py-[1%]" />

        <div class="user_content px-[2%]">
            <h1 class="text-[30px] uppercase font-bold mb-4" :class="[
                darkMode ? 'text-white' : 'text-[#283F69]',
            ]">
                Listado de usuarios
            </h1>

            <div class="flex flex-wrap justify-between items-center !mb-4 gap-4">
                <div class="flex items-center gap-2 text-sm">
                    <label :class="[darkMode ? 'text-white' : 'text-[#283F69]']">Registros por página:</label>
                    <select v-model.number="perPage" @change="handlePerPageChange"
                        class="border border-gray-300 rounded px-3 py-2 text-sm  dark:text-white dark:bg-gray-700 dark:border-gray-600"
                        :class="[
                            darkMode ? 'bg-white text-black' : 'bg-white border-gray-300',
                        ]">
                        <option v-for="option in perPageOptions" :key="`per-page-${option}`" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>
                <div class="text-sm" :class="[darkMode ? 'text-white' : 'text-[#283F69]']">
                    Página {{ pagination.current_page }} de {{ pagination.last_page }} — Total: {{ pagination.total }}
                </div>
            </div>

            <div v-if="cargando" class="text-gray-500 text-center py-10 text-lg">
                Cargando usuarios...
            </div>

            <div v-else class="overflow-x-auto rounded-lg">
                <table class="min-w-full border border-gray-200" :class="[
                    darkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray'
                ]">
                    <thead class="" :class="[
                        darkMode ? '!bg-white text-[#283F69]' : 'bg-[#283F69] text-white',
                    ]">
                        <tr>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">#</th>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">Nombre</th>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">Email</th>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">Teléfono</th>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">Documento</th>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">
                                Grupo de usuario
                            </th>
                            <th class="px-4 py-3 text-center text-[20px] !font-semibold">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Si no hay usuarios -->
                        <tr v-if="users.length === 0">
                            <td colspan="5" class="text-center py-6 text-gray-500 dark:text-gray-400 text-[15px]">
                                No hay usuarios registrados.
                            </td>
                        </tr>

                        <!-- Si hay usuarios -->
                        <tr v-else v-for="(user, index) in users" :key="user.id"
                            class=" dark:hover:bg-gray-700 transition" :class="[
                                darkMode ? 'bg-gray-800 hover:bg-gray-700' : 'bg-white hover:bg-gray-100',
                            ]">
                            <td class="p-[1%] text-[15px] text-gray-800 dark:text-gray-200 text-center" :class="[
                                darkMode ? 'text-white' : 'text-[#283F69]',
                            ]">
                                {{ index + 1 }}
                            </td>
                            <td class="p-[1%] text-[15px] text-gray-800 dark:text-gray-200 text-center" :class="[
                                darkMode ? 'text-white' : 'text-[#283F69]',
                            ]">
                                {{ user.name }} {{ user.surname }}
                            </td>
                            <td class="p-[1%] text-[15px] text-gray-800 dark:text-gray-200 text-center" :class="[
                                darkMode ? 'text-white' : 'text-[#283F69]',
                            ]">
                                {{ user.email }}
                            </td>
                            <td class="p-[1%] text-[15px] text-gray-800 dark:text-gray-200 text-center" :class="[
                                darkMode ? 'text-white' : 'text-[#283F69]',
                            ]">
                                {{ user.phone }}
                            </td>
                            <td class="p-[1%] text-[15px] text-gray-800 dark:text-gray-200 text-center" :class="[
                                darkMode ? 'text-white' : 'text-[#283F69]',
                            ]">
                                <strong class="uppercase text-[17px]">{{ user.type_document }} - </strong> {{
                                    user.document }}
                            </td>
                            <td class="p-[1%] text-[15px] text-gray-800 dark:text-gray-200 text-center" :class="[
                                darkMode ? 'text-white' : 'text-[#283F69]',
                            ]">
                                {{ user.group_name || 'Sin grupo' }}
                            </td>
                            <td class="p-[1%] text-center flex justify-around w-[100%] items-center">
                                <button class="bg-blue-600 text-white font-bold flex items-center gap-2 p-[2%] rounded 
                                    transition-all duration-300 ease-in-out transform hover:scale-105
                                    hover:bg-blue-700 dark:text-blue-400 dark:hover:text-blue-300 mx-2"
                                    @click="editarUsuario(user)" title="Editar">
                                    Editar
                                    <SquarePen class="w-6 h-6" />
                                </button>

                                <button class="bg-red-600 text-white font-bold flex items-center gap-2 p-[2%] rounded 
                                    transition-all duration-300 ease-in-out transform hover:scale-105
                                    hover:bg-red-700 dark:text-red-400 dark:hover:text-red-300 mx-2"
                                    @click="eliminarUsuario(user.id)" title="Eliminar">
                                    Eliminar
                                    <Trash2 class="w-6 h-6" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex flex-wrap justify-between items-center !mt-4 gap-4">
                    <span class="text-sm" :class="[darkMode ? 'text-white' : 'text-[#283F69]']">
                        Mostrando página {{ pagination.current_page }} de {{ pagination.last_page }}
                    </span>
                    <div class="flex items-center gap-2">
                        <button @click="prevPage" :disabled="currentPage <= 1"
                            class="px-4 py-2 border rounded transition-all"
                            :class="[currentPage <= 1 ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'text-[#283F69] border-[#283F69] hover:bg-[#283F69] hover:text-white']">
                            Anterior
                        </button>
                        <button @click="nextPage" :disabled="currentPage >= pagination.last_page"
                            class="px-4 py-2 border rounded transition-all"
                            :class="[currentPage >= pagination.last_page ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'text-[#283F69] border-[#283F69] hover:bg-[#283F69] hover:text-white']">
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
