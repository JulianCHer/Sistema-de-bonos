import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/main.css'

const app = createApp(App)

const token = localStorage.getItem('token')
if (token) {
  console.log('Token activo encontrado')
} else {
  console.log('No hay sesión activa')
}

app.use(router)

app.mount('#app')
