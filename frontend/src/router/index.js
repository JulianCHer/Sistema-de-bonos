import { createRouter, createWebHistory } from 'vue-router'
import LoginBonos from '../views/Login.vue'
import Sorteo from '../views/SorteoView.vue'
import Usuario from '../views/UsuarioView.vue'

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
      path: '/sorteo',
      name: 'sorteo',
      component: Sorteo,
      meta: { requiresAuth: true },
    },
    {
      path: '/usuarios',
      name: 'usuarios',
      component: Usuario,
      meta: { requiresAuth: true },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/NotFound.vue')
    }
    
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
      path: '/sorteo', replace: true 
    })
  }else{
    next()
  }
})

export default router
