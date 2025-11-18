import axios from 'axios'

// URL base de tu API
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: false,
})

// ================================
// 🔹 Interceptor de REQUEST
// ================================
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error)
)

// ================================
// 🔹 Interceptor de RESPONSE (manejo global de errores)
// ================================
api.interceptors.response.use(
  response => response,

  error => {
    // ⚠️ Caso 1: token inválido o expirado
    if (error.response && error.response.status === 401) {
      console.warn('Token inválido o expirado, redirigiendo al login...')
      localStorage.removeItem('token')
      window.location.href = '/login'
    }

    // ⚠️ Caso 2: error de conexión a base de datos (devuelto por Laravel)
    if (error.response && error.response.data?.status === 'db_error') {
      alert('⚠️ Se perdió la conexión con la base de datos.\nPor seguridad serás deslogueado.')
      localStorage.removeItem('token')
      window.location.href = '/login'
    }

    // ⚠️ Caso 3: el servidor no responde (fallo total de red o backend caído)
    if (error.code === 'ERR_NETWORK' || !error.response) {
      alert('🚫 Error de conexión con el servidor. Verifica tu red o contacta con soporte.')
      localStorage.removeItem('token')
      window.location.href = '/'
    }

    return Promise.reject(error)
  }
)

export default api
