import { createRouter, createWebHistory } from 'vue-router'
import LoginBonos from '../views/Login.vue'
import DashboardView from '../views/DashboardView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: LoginBonos,
      meta: { requiresAuth: false },
    },

    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next({ 
      path: '/', replace: true 
    })
  }else if(to.path === '/' && token){
    next({
      path: '/dashboard', replace: true 
    })
  }else{
    next()
  }
})

export default router
