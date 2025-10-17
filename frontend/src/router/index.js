import { createRouter, createWebHistory } from 'vue-router'
import LoginBonos  from '@/components/LoginBonos.vue' 

const routes = [
  { path: '/', name: 'home', component: LoginBonos },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
