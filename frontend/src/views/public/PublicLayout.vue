<template>
  <div class="layout">
    <header class="navbar" :class="{ scrolled: isScrolled }">
      <div class="nav-container">
        <RouterLink to="/" class="nav-logo">
          <span class="logo-icon">🏫</span>
          <span class="logo-text">Vidusskola</span>
        </RouterLink>

        <nav class="nav-links" :class="{ open: menuOpen }">
          <RouterLink to="/" @click="menuOpen = false">Sākums</RouterLink>
          <RouterLink to="/jaunumi" @click="menuOpen = false">Jaunumi</RouterLink>
          <RouterLink to="/vesture" @click="menuOpen = false">Vēsture</RouterLink>
          <RouterLink to="/kontakti" @click="menuOpen = false">Kontakti</RouterLink>
          <RouterLink to="/stundu-saraksts" @click="menuOpen = false">Stundu saraksts</RouterLink>

          <div class="nav-dropdown">
            <button class="dropdown-trigger">Vairāk <span class="arrow">▾</span></button>
            <div class="dropdown-menu">
              <RouterLink to="/skoleniem" @click="menuOpen = false">Skolēniem</RouterLink>
              <RouterLink to="/skolotajiem" @click="menuOpen = false">Skolotājiem</RouterLink>
            </div>
          </div>

          <RouterLink
            v-if="authStore.isAuthenticated"
            to="/admin"
            class="nav-admin"
            @click="menuOpen = false"
          >Admin panelis</RouterLink>
          <RouterLink
            v-else
            to="/login"
            class="nav-admin"
            @click="menuOpen = false"
          >Admin panelis</RouterLink>

          <button class="dark-toggle" @click="toggleDark" :title="isDark ? 'Gaišs režīms' : 'Tumšs režīms'">
            {{ isDark ? '☀️' : '🌙' }}
          </button>
        </nav>

        <div class="nav-actions">
          <button class="dark-toggle desktop-only" @click="toggleDark">
            {{ isDark ? '☀️' : '🌙' }}
          </button>
          <button class="hamburger" @click="menuOpen = !menuOpen" :class="{ active: menuOpen }">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <main class="main-content">
      <RouterView v-slot="{ Component }">
        <Transition name="page" mode="out-in">
          <component :is="Component" />
        </Transition>
      </RouterView>
    </main>

    <footer class="footer">
      <div class="footer-container">
        <div class="footer-grid">
          <div class="footer-col">
            <div class="footer-logo">🏫 Vidusskola</div>
            <p class="footer-desc">Kvalitatīva izglītība un audzināšana kopš 1920. gada. Mūsu mērķis — izglītots, radošs un atbildīgs cilvēks.</p>
          </div>
          <div class="footer-col">
            <h4>Kontakti</h4>
            <ul class="footer-list">
              <li>📍 Brīvības iela 42, Rīga, LV-1010</li>
              <li>📞 +371 67 000 000</li>
              <li>✉️ info@vidusskola.lv</li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Bibliotēka</h4>
            <ul class="footer-list">
              <li>Pirmdiena – Piektdiena</li>
              <li>8:00 – 17:00</li>
              <li>Sestdiena: 10:00 – 14:00</li>
              <li>Svētdiena: slēgta</li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Saites</h4>
            <ul class="footer-links">
              <li><RouterLink to="/jaunumi">Jaunumi</RouterLink></li>
              <li><RouterLink to="/stundu-saraksts">Stundu saraksts</RouterLink></li>
              <li><RouterLink to="/skoleniem">Skolēniem</RouterLink></li>
              <li><RouterLink to="/skolotajiem">Skolotājiem</RouterLink></li>
              <li><RouterLink to="/vesture">Vēsture</RouterLink></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>© {{ new Date().getFullYear() }} Vidusskola. Visas tiesības aizsargātas.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const menuOpen = ref(false)
const isScrolled = ref(false)
const isDark = ref(localStorage.getItem('darkMode') === 'true')

function toggleDark() {
  isDark.value = !isDark.value
  document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : '')
  localStorage.setItem('darkMode', isDark.value)
}

function handleScroll() {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<style scoped>
.layout { display: flex; flex-direction: column; min-height: 100vh; }
.main-content { flex: 1; }

/* NAVBAR */
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(250, 249, 247, 0.92);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid transparent;
  transition: all var(--transition);
}
.navbar.scrolled {
  border-bottom-color: var(--clr-border);
  box-shadow: var(--shadow-sm);
}
[data-theme="dark"] .navbar {
  background: rgba(17, 23, 20, 0.92);
}

.nav-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: 68px;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-logo {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  text-decoration: none;
  flex-shrink: 0;
}
.logo-icon { font-size: 1.5rem; }
.logo-text {
  font-family: var(--font-display);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--clr-accent);
  letter-spacing: -0.02em;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  flex: 1;
}

.nav-links > a {
  position: relative;
  padding: 0.45rem 0.8rem;
  border-radius: var(--radius);
  color: var(--clr-text);
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  transition: all var(--transition);
  overflow: hidden;
}

.nav-links > a::before {
  content: '';
  position: absolute;
  inset: 0;
  background: var(--clr-accent);
  transform: scaleX(0);
  transform-origin: left;
  border-radius: var(--radius);
  transition: transform 0.3s ease;
  z-index: -1;
}

.nav-links > a:hover::before,
.nav-links > a.router-link-active::before {
  transform: scaleX(1);
}

.nav-links > a:hover,
.nav-links > a.router-link-exact-active {
  color: #fff;
}

/* Dropdown */
.nav-dropdown {
  position: relative;
}
.dropdown-trigger {
  background: none;
  border: none;
  padding: 0.45rem 0.8rem;
  color: var(--clr-text);
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  border-radius: var(--radius);
  transition: all var(--transition);
}
.dropdown-trigger:hover { background: var(--clr-surface2); }
.arrow { font-size: 0.7rem; transition: transform 0.2s; }
.nav-dropdown:hover .arrow { transform: rotate(180deg); }

.dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  min-width: 180px;
  overflow: hidden;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: all 0.2s ease;
}
.nav-dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}
.dropdown-menu a {
  display: block;
  padding: 0.65rem 1rem;
  color: var(--clr-text);
  font-size: 0.9rem;
  text-decoration: none;
  transition: background var(--transition);
}
.dropdown-menu a:hover { background: var(--clr-accent-pale); color: var(--clr-accent); }

.nav-admin {
  margin-left: auto;
  background: var(--clr-accent) !important;
  color: #fff !important;
  padding: 0.45rem 1rem !important;
  border-radius: var(--radius) !important;
}
.nav-admin::before { display: none !important; }
.nav-admin:hover { background: var(--clr-accent-light) !important; opacity: 0.9; }

.dark-toggle {
  background: none;
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  width: 36px;
  height: 36px;
  font-size: 1rem;
  cursor: pointer;
  transition: background var(--transition);
}
.dark-toggle:hover { background: var(--clr-surface2); }

.nav-actions { display: flex; align-items: center; gap: 0.5rem; margin-left: auto; }
.desktop-only { display: flex; }

/* Hamburger */
.hamburger {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 40px;
  height: 40px;
  background: none;
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  cursor: pointer;
  padding: 0 10px;
}
.hamburger span {
  display: block;
  height: 2px;
  background: var(--clr-text);
  border-radius: 2px;
  transition: all 0.3s;
}
.hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
.hamburger.active span:nth-child(2) { opacity: 0; }
.hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

/* FOOTER */
.footer {
  background: var(--clr-accent);
  color: rgba(255,255,255,0.9);
  margin-top: auto;
}
.footer-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 3rem 2rem 1.5rem;
}
.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
  gap: 2.5rem;
  margin-bottom: 2rem;
}
.footer-logo {
  font-family: var(--font-display);
  font-size: 1.3rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 0.75rem;
}
.footer-desc { font-size: 0.875rem; line-height: 1.7; opacity: 0.8; }
.footer-col h4 {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: rgba(255,255,255,0.6);
  margin-bottom: 1rem;
}
.footer-list { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
.footer-list li { font-size: 0.875rem; opacity: 0.85; }
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 0.4rem; }
.footer-links a { color: rgba(255,255,255,0.8); font-size: 0.875rem; text-decoration: none; transition: color var(--transition); }
.footer-links a:hover { color: #fff; }
.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.15);
  padding-top: 1.25rem;
  text-align: center;
  font-size: 0.8rem;
  opacity: 0.6;
}

/* Page transitions */
.page-enter-active,
.page-leave-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.page-enter-from { opacity: 0; transform: translateY(12px); }
.page-leave-to { opacity: 0; transform: translateY(-8px); }

/* Mobile */
@media (max-width: 900px) {
  .nav-links {
    display: none;
    position: fixed;
    top: 68px;
    left: 0;
    right: 0;
    background: var(--clr-surface);
    flex-direction: column;
    padding: 1.5rem;
    gap: 0.25rem;
    border-bottom: 1px solid var(--clr-border);
    box-shadow: var(--shadow-md);
    align-items: flex-start;
  }
  .nav-links.open { display: flex; }
  .nav-links > a { width: 100%; }
  .nav-admin { margin-left: 0 !important; }
  .desktop-only { display: none; }
  .hamburger { display: flex; }
  .dropdown-menu { position: static; opacity: 1; visibility: visible; transform: none; box-shadow: none; border: none; background: var(--clr-surface2); }
  .nav-dropdown { width: 100%; }
  .dropdown-trigger { width: 100%; }
  .dark-toggle.desktop-only { display: none; }

  .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
}

@media (max-width: 550px) {
  .footer-grid { grid-template-columns: 1fr; }
}
</style>
