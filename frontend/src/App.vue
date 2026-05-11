<template>
  <RouterView />
</template>

<script setup>
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

onMounted(async () => {
  // Validate token on load
  if (authStore.token) {
    await authStore.fetchUser()
  }
  // Apply saved dark mode preference
  if (localStorage.getItem('darkMode') === 'true') {
    document.documentElement.setAttribute('data-theme', 'dark')
  }
})
</script>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --clr-bg: #faf9f7;
  --clr-surface: #ffffff;
  --clr-surface2: #f3f1ee;
  --clr-border: #e5e2dc;
  --clr-text: #1a1714;
  --clr-text-muted: #6b6560;
  --clr-accent: #1a4a2e;
  --clr-accent-light: #2d7a4e;
  --clr-accent-pale: #e8f4ed;
  --clr-gold: #c9a84c;
  --clr-error: #c0392b;
  --clr-success: #27ae60;
  --clr-warning: #e67e22;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.10);
  --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
  --radius: 8px;
  --radius-lg: 16px;
  --font-display: 'Playfair Display', Georgia, serif;
  --font-body: 'DM Sans', system-ui, sans-serif;
  --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

[data-theme="dark"] {
  --clr-bg: #111714;
  --clr-surface: #1a2420;
  --clr-surface2: #1f2e28;
  --clr-border: #2a3d34;
  --clr-text: #e8ede9;
  --clr-text-muted: #8aada0;
  --clr-accent: #3db871;
  --clr-accent-light: #5cd08c;
  --clr-accent-pale: #1a3526;
  --clr-gold: #e0b860;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.35);
  --shadow-lg: 0 8px 32px rgba(0,0,0,0.4);
}

html { font-size: 16px; }

body {
  font-family: var(--font-body);
  background: var(--clr-bg);
  color: var(--clr-text);
  line-height: 1.6;
  transition: background var(--transition), color var(--transition);
}

a { color: var(--clr-accent); text-decoration: none; }
a:hover { color: var(--clr-accent-light); }

img { max-width: 100%; display: block; }

button { cursor: pointer; font-family: var(--font-body); }

/* Scrollbar */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--clr-bg); }
::-webkit-scrollbar-thumb { background: var(--clr-border); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--clr-text-muted); }

/* Fade-in animation */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(24px); }
  to { opacity: 1; transform: translateY(0); }
}

.fade-in {
  animation: fadeInUp 0.6s ease forwards;
}

.fade-in-delay-1 { animation-delay: 0.1s; opacity: 0; }
.fade-in-delay-2 { animation-delay: 0.2s; opacity: 0; }
.fade-in-delay-3 { animation-delay: 0.3s; opacity: 0; }
.fade-in-delay-4 { animation-delay: 0.4s; opacity: 0; }

/* Utility */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.4rem;
  border-radius: var(--radius);
  font-weight: 500;
  font-size: 0.9rem;
  border: none;
  transition: all var(--transition);
  white-space: nowrap;
}

.btn-primary {
  background: var(--clr-accent);
  color: #fff;
}
.btn-primary:hover { background: var(--clr-accent-light); transform: translateY(-1px); box-shadow: var(--shadow-md); }

.btn-secondary {
  background: var(--clr-surface2);
  color: var(--clr-text);
  border: 1px solid var(--clr-border);
}
.btn-secondary:hover { background: var(--clr-border); }

.btn-danger {
  background: var(--clr-error);
  color: #fff;
}
.btn-danger:hover { background: #a93226; transform: translateY(-1px); }

.btn-sm { padding: 0.4rem 0.9rem; font-size: 0.82rem; }

.btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; }

/* Form elements */
.form-group { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1.2rem; }
.form-group label { font-size: 0.875rem; font-weight: 500; color: var(--clr-text-muted); }

.form-control {
  padding: 0.6rem 0.9rem;
  border: 1.5px solid var(--clr-border);
  border-radius: var(--radius);
  background: var(--clr-surface);
  color: var(--clr-text);
  font-family: var(--font-body);
  font-size: 0.95rem;
  transition: border-color var(--transition), box-shadow var(--transition);
  outline: none;
}
.form-control:focus { border-color: var(--clr-accent); box-shadow: 0 0 0 3px var(--clr-accent-pale); }
textarea.form-control { resize: vertical; min-height: 120px; }

/* Alerts */
.alert {
  padding: 0.75rem 1rem;
  border-radius: var(--radius);
  margin-bottom: 1rem;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.alert-error { background: #fdecea; color: var(--clr-error); border: 1px solid #f5c6c2; }
.alert-success { background: #e8f8ee; color: var(--clr-success); border: 1px solid #c3e6cc; }
.alert-warning { background: #fef6e4; color: var(--clr-warning); border: 1px solid #fce0b3; }

[data-theme="dark"] .alert-error { background: #2d1b1b; border-color: #5c2e2e; }
[data-theme="dark"] .alert-success { background: #1a3025; border-color: #2d5a3d; }

/* Loading spinner */
.spinner {
  width: 36px; height: 36px;
  border: 3px solid var(--clr-border);
  border-top-color: var(--clr-accent);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 2rem auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Card */
.card {
  background: var(--clr-surface);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--clr-border);
  overflow: hidden;
  transition: transform var(--transition), box-shadow var(--transition);
}
.card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.card-body { padding: 1.5rem; }

/* Modal overlay */
.modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.5);
  backdrop-filter: blur(4px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: fadeInUp 0.2s ease;
}

.modal {
  background: var(--clr-surface);
  border-radius: var(--radius-lg);
  padding: 2rem;
  max-width: 480px;
  width: 100%;
  box-shadow: var(--shadow-lg);
}

.modal-title {
  font-family: var(--font-display);
  font-size: 1.3rem;
  margin-bottom: 1rem;
  color: var(--clr-text);
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

/* Badge */
.badge {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 600;
}
.badge-green { background: var(--clr-accent-pale); color: var(--clr-accent); }
.badge-gold { background: #fdf3dc; color: #8a6a10; }

[data-theme="dark"] .badge-gold { background: #2d2510; color: var(--clr-gold); }
</style>
