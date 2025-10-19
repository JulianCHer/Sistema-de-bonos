import { createRouter, createWebHistory } from 'vue-router'
import LoginBonos from '../views/Login.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: LoginBonos,
    },
  ],
})

export default router
