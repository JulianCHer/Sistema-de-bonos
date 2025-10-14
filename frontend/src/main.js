import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.baseURL = "http://127.0.0.1:8000";

axios.get("/sanctum/csrf-cookie")
    .then(() => console.log("✅ Conexión correcta con Laravel Sanctum"))
    .catch(err => console.error("❌ Error al conectar:", err));

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
