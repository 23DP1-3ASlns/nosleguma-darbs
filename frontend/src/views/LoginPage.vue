<template>
  <div class="login-page">
    <div class="login-card">
      <RouterLink to="/" class="login-back">← Atpakaļ uz sākumu</RouterLink>

      <div class="login-header">
        <div class="login-logo">🏫</div>
        <h1 class="login-title">Admin panelis</h1>
        <p class="login-subtitle">Pierakstieties, lai turpinātu</p>
      </div>

      <div v-if="error" class="alert alert-error">⚠️ {{ error }}</div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>E-pasta adrese</label>
          <input
            v-model="form.email"
            type="email"
            class="form-control"
            placeholder="admin@skola.lv"
            required
            autocomplete="email"
          />
        </div>
        <div class="form-group">
          <label>Parole</label>
          <div class="password-wrap">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-control"
              placeholder="••••••••"
              required
              autocomplete="current-password"
            />
            <button type="button" class="password-toggle" @click="showPassword = !showPassword">
              {{ showPassword ? '🙈' : '👁️' }}
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary login-btn" :disabled="loading">
          <span v-if="loading">Notiek pierakstīšanās...</span>
          <span v-else>Pierakstīties</span>
        </button>
      </form>

      <p class="login-hint">Tikai autorizēti administrators var piekļūt šim panelim.</p>
    </div>

    <div class="login-bg">
      <div class="bg-shape"></div>
      <div class="bg-text">
        <span class="bg-title">Vidusskola</span>
        <span class="bg-subtitle">Administratoru panelis</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)
const showPassword = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await authStore.login(form.value.email, form.value.password)
    const redirect = route.query.redirect || '/admin'
    router.push(redirect)
  } catch (e) {
    const msg = e.response?.data?.errors?.email?.[0]
      || e.response?.data?.message
      || 'Pieteikšanās neizdevās. Mēģiniet vēlreiz.'
    error.value = msg
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.login-card {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem 4rem;
  background: var(--clr-bg);
}

.login-back {
  font-size: 0.875rem;
  color: var(--clr-text-muted);
  margin-bottom: 3rem;
  text-decoration: none;
  transition: color var(--transition);
}
.login-back:hover { color: var(--clr-accent); }

.login-logo { font-size: 3rem; margin-bottom: 1rem; }
.login-title {
  font-family: var(--font-display);
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
}
.login-subtitle { color: var(--clr-text-muted); margin-bottom: 2rem; }

.login-form { max-width: 400px; }

.password-wrap { position: relative; }
.password-wrap .form-control { padding-right: 3rem; }
.password-toggle {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  padding: 0;
}

.login-btn { width: 100%; justify-content: center; padding: 0.85rem; font-size: 1rem; margin-top: 0.5rem; }

.login-hint { font-size: 0.78rem; color: var(--clr-text-muted); margin-top: 1.5rem; max-width: 360px; }

/* Right panel */
.login-bg {
  background: linear-gradient(135deg, var(--clr-accent) 0%, #0d2b1a 100%);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.bg-shape {
  position: absolute;
  width: 500px;
  height: 500px;
  border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.1);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.bg-shape::before {
  content: '';
  position: absolute;
  inset: 40px;
  border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.07);
}

.bg-text { position: relative; z-index: 1; text-align: center; color: white; }
.bg-title { display: block; font-family: var(--font-display); font-size: 3rem; font-weight: 700; letter-spacing: -0.03em; }
.bg-subtitle { display: block; color: rgba(255,255,255,0.6); font-size: 1rem; margin-top: 0.5rem; }

@media (max-width: 768px) {
  .login-page { grid-template-columns: 1fr; }
  .login-bg { display: none; }
  .login-card { padding: 2rem 1.5rem; }
}
</style>
