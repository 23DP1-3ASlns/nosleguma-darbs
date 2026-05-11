import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
  const token = ref(localStorage.getItem('auth_token') || null)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin' || user.value?.role === 'superadmin')

  async function login(email, password) {
    const response = await axios.post('/login', { email, password })
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('auth_token', token.value)
    localStorage.setItem('auth_user', JSON.stringify(user.value))
    return response.data
  }

  async function logout() {
    try {
      await axios.post('/logout')
    } catch (e) {
      // ignore
    }
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
  }

  async function fetchUser() {
    if (!token.value) return null
    try {
      const response = await axios.get('/user')
      user.value = response.data
      localStorage.setItem('auth_user', JSON.stringify(user.value))
      return user.value
    } catch {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      return null
    }
  }

  return { user, token, isAuthenticated, isAdmin, login, logout, fetchUser }
})
