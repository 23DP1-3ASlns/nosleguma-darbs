import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  // Public routes
  {
    path: '/',
    component: () => import('../views/public/PublicLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('../views/public/HomePage.vue') },
      { path: 'jaunumi', name: 'news', component: () => import('../views/public/NewsPage.vue') },
      { path: 'vesture', name: 'history', component: () => import('../views/public/HistoryPage.vue') },
      { path: 'kontakti', name: 'contacts', component: () => import('../views/public/ContactsPage.vue') },
      { path: 'stundu-saraksts', name: 'schedule', component: () => import('../views/public/SchedulePage.vue') },
      { path: 'skoleniem', name: 'students', component: () => import('../views/public/StudentsPage.vue') },
      { path: 'skolotajiem', name: 'teachers', component: () => import('../views/public/TeachersPage.vue') },
    ]
  },

  // Auth
  { path: '/login', name: 'login', component: () => import('../views/LoginPage.vue') },

  // Admin routes
  {
    path: '/admin',
    component: () => import('../views/admin/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'admin-dashboard', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'news', name: 'admin-news', component: () => import('../views/admin/NewsManagement.vue') },
      { path: 'schedule', name: 'admin-schedule', component: () => import('../views/admin/ScheduleManagement.vue') },
      { path: 'pages', name: 'admin-pages', component: () => import('../views/admin/PageEditor.vue') },
      { path: 'users', name: 'admin-users', component: () => import('../views/admin/UserManagement.vue') },
    ]
  },

  // Catch all
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      next({ name: 'login', query: { redirect: to.fullPath } })
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
