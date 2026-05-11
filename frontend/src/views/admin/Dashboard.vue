<template>
  <div class="dashboard">
    <div class="dashboard-welcome">
      <h2 class="welcome-title">Labrīt, {{ authStore.user?.name?.split(' ')[0] }}! 👋</h2>
      <p class="welcome-sub">Šeit ir pārskats par jūsu skolas mājaslapu.</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card" v-for="stat in stats" :key="stat.label">
        <div class="stat-icon">{{ stat.icon }}</div>
        <div class="stat-info">
          <div class="stat-value">{{ stat.value }}</div>
          <div class="stat-label">{{ stat.label }}</div>
        </div>
      </div>
    </div>

    <div class="quick-actions">
      <h3 class="section-heading">Ātrās darbības</h3>
      <div class="actions-grid">
        <RouterLink v-for="action in actions" :key="action.to" :to="action.to" class="action-card">
          <span class="action-icon">{{ action.icon }}</span>
          <span class="action-label">{{ action.label }}</span>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()

const stats = ref([
  { icon: '📰', label: 'Jaunumu ieraksti', value: '...' },
  { icon: '📅', label: 'Stundu ieraksti', value: '...' },
  { icon: '👥', label: 'Administratori', value: '...' },
])

const actions = [
  { to: '/admin/news', icon: '✏️', label: 'Pievienot jaunumu' },
  { to: '/admin/schedule', icon: '📅', label: 'Pārvaldīt sarakstu' },
  { to: '/admin/pages', icon: '📄', label: 'Rediģēt lapas' },
  { to: '/admin/users', icon: '👤', label: 'Pārvaldīt lietotājus' },
]

onMounted(async () => {
  try {
    const [newsRes, scheduleRes, usersRes] = await Promise.all([
      axios.get('/news'),
      axios.get('/schedule'),
      axios.get('/admin/users'),
    ])
    stats.value[0].value = newsRes.data.length
    stats.value[1].value = scheduleRes.data.length
    stats.value[2].value = usersRes.data.length
  } catch {}
})
</script>

<style scoped>
.dashboard-welcome { margin-bottom: 2rem; }
.welcome-title { font-family: var(--font-display); font-size: 1.8rem; font-weight: 700; margin-bottom: 0.4rem; }
.welcome-sub { color: var(--clr-text-muted); }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2.5rem;
}

.stat-card {
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform var(--transition), box-shadow var(--transition);
}
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.stat-icon { font-size: 2rem; }
.stat-value { font-size: 1.8rem; font-weight: 700; font-family: var(--font-display); color: var(--clr-accent); line-height: 1; }
.stat-label { font-size: 0.8rem; color: var(--clr-text-muted); margin-top: 0.25rem; }

.section-heading { font-family: var(--font-display); font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; }

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 1.75rem 1.25rem;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  text-decoration: none;
  color: var(--clr-text);
  font-weight: 500;
  font-size: 0.9rem;
  text-align: center;
  transition: all var(--transition);
}
.action-card:hover { background: var(--clr-accent); color: white; transform: translateY(-3px); box-shadow: var(--shadow-md); }
.action-icon { font-size: 1.75rem; }
</style>
