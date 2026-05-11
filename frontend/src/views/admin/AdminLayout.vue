<template>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
      <div class="sidebar-header">
        <RouterLink to="/" class="sidebar-logo">
          <span class="logo-icon">🏫</span>
          <span class="logo-text">Vidusskola</span>
        </RouterLink>
        <button class="sidebar-toggle" @click="sidebarCollapsed = !sidebarCollapsed">
          {{ sidebarCollapsed ? '→' : '←' }}
        </button>
      </div>

      <nav class="sidebar-nav">
        <RouterLink to="/admin" class="nav-item" exact>
          <span class="nav-icon">📊</span>
          <span class="nav-text">Pārskats</span>
        </RouterLink>
        <RouterLink to="/admin/news" class="nav-item">
          <span class="nav-icon">📰</span>
          <span class="nav-text">Jaunumi</span>
        </RouterLink>
        <RouterLink to="/admin/schedule" class="nav-item">
          <span class="nav-icon">📅</span>
          <span class="nav-text">Stundu saraksts</span>
        </RouterLink>
        <RouterLink to="/admin/pages" class="nav-item">
          <span class="nav-icon">📄</span>
          <span class="nav-text">Lapas</span>
        </RouterLink>
        <RouterLink to="/admin/users" class="nav-item">
          <span class="nav-icon">👥</span>
          <span class="nav-text">Lietotāji</span>
        </RouterLink>
      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-avatar">{{ initials }}</div>
          <div class="user-details" v-if="!sidebarCollapsed">
            <span class="user-name">{{ authStore.user?.name }}</span>
            <span class="user-role badge badge-green">{{ authStore.user?.role }}</span>
          </div>
        </div>
        <button class="logout-btn" @click="handleLogout" :title="sidebarCollapsed ? 'Izrakstīties' : ''">
          <span>🚪</span>
          <span class="nav-text">Izrakstīties</span>
        </button>
      </div>
    </aside>

    <!-- Main -->
    <div class="admin-main">
      <header class="admin-topbar">
        <button class="mobile-menu-btn" @click="sidebarCollapsed = !sidebarCollapsed">☰</button>
        <div class="topbar-title">{{ currentPageTitle }}</div>
        <RouterLink to="/" class="topbar-link">← Skatīt mājaslapa</RouterLink>
      </header>

      <main class="admin-content">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const sidebarCollapsed = ref(false)

const initials = computed(() => {
  const name = authStore.user?.name || 'A'
  return name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
})

const pageTitles = {
  '/admin': 'Pārskats',
  '/admin/news': 'Jaunumu pārvaldība',
  '/admin/schedule': 'Stundu saraksts',
  '/admin/pages': 'Lapu redaktors',
  '/admin/users': 'Lietotāju pārvaldība',
}

const currentPageTitle = computed(() => pageTitles[route.path] || 'Admin panelis')

async function handleLogout() {
  await authStore.logout()
  router.push('/')
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: var(--clr-bg);
}

/* Sidebar */
.sidebar {
  width: 260px;
  flex-shrink: 0;
  background: var(--clr-surface);
  border-right: 1px solid var(--clr-border);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  transition: width var(--transition);
}

.sidebar.collapsed { width: 72px; }

.sidebar-header {
  padding: 1.25rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--clr-border);
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  text-decoration: none;
  color: var(--clr-accent);
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 1.1rem;
  overflow: hidden;
  white-space: nowrap;
}

.logo-icon { font-size: 1.4rem; flex-shrink: 0; }
.collapsed .logo-text { display: none; }

.sidebar-toggle {
  background: none;
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  width: 28px;
  height: 28px;
  font-size: 0.75rem;
  cursor: pointer;
  color: var(--clr-text-muted);
  flex-shrink: 0;
  transition: background var(--transition);
}
.sidebar-toggle:hover { background: var(--clr-surface2); }

.sidebar-nav {
  flex: 1;
  padding: 1rem 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.75rem;
  border-radius: var(--radius);
  color: var(--clr-text-muted);
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all var(--transition);
  white-space: nowrap;
  overflow: hidden;
}
.nav-item:hover { background: var(--clr-surface2); color: var(--clr-text); }
.nav-item.router-link-exact-active {background: var(--clr-accent-pale); color: var(--clr-accent); font-weight: 600;}

.nav-icon { font-size: 1.1rem; flex-shrink: 0; }
.collapsed .nav-text { display: none; }

.sidebar-footer {
  padding: 0.75rem;
  border-top: 1px solid var(--clr-border);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.25rem;
  overflow: hidden;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--clr-accent);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
  flex-shrink: 0;
}

.user-details { display: flex; flex-direction: column; overflow: hidden; }
.user-name { font-size: 0.875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role { font-size: 0.7rem; }

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.75rem;
  border-radius: var(--radius);
  background: none;
  border: none;
  color: var(--clr-error);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: background var(--transition);
  width: 100%;
  text-align: left;
}
.logout-btn:hover { background: #fdecea; }
[data-theme="dark"] .logout-btn:hover { background: #2d1b1b; }
.collapsed .logout-btn .nav-text { display: none; }

/* Main area */
.admin-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

.admin-topbar {
  height: 60px;
  border-bottom: 1px solid var(--clr-border);
  background: var(--clr-surface);
  display: flex;
  align-items: center;
  padding: 0 1.5rem;
  gap: 1rem;
  position: sticky;
  top: 0;
  z-index: 50;
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  width: 36px;
  height: 36px;
  font-size: 1rem;
  cursor: pointer;
}

.topbar-title {
  font-weight: 600;
  font-size: 1rem;
  flex: 1;
  color: var(--clr-text);
}

.topbar-link {
  font-size: 0.82rem;
  color: var(--clr-text-muted);
  text-decoration: none;
  transition: color var(--transition);
}
.topbar-link:hover { color: var(--clr-accent); }

.admin-content { flex: 1; padding: 2rem; }

@media (max-width: 768px) {
  .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 200; transform: translateX(-100%); }
  .sidebar:not(.collapsed) { transform: translateX(0); }
  .admin-main { margin-left: 0; }
  .mobile-menu-btn { display: flex; align-items: center; justify-content: center; }
  .admin-content { padding: 1rem; }
}
</style>
