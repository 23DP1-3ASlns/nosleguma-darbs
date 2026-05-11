<template>
  <div class="user-mgmt">
    <div class="page-header">
      <h2 class="page-title">Lietotāju pārvaldība</h2>
      <button class="btn btn-primary" @click="openModal()">+ Jauns lietotājs</button>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else>
      <div class="users-table-wrap">
        <table class="users-table">
          <thead>
            <tr>
              <th>Vārds</th>
              <th>E-pasts</th>
              <th>Loma</th>
              <th>Reģistrēts</th>
              <th>Darbības</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" :class="{ self: user.id === authStore.user?.id }">
              <td>
                <div class="user-cell">
                  <div class="avatar">{{ initials(user.name) }}</div>
                  <span>{{ user.name }}</span>
                </div>
              </td>
              <td>{{ user.email }}</td>
              <td>
                <span class="badge" :class="user.role === 'superadmin' ? 'badge-gold' : 'badge-green'">
                  {{ user.role }}
                </span>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td>
                <button
                  v-if="user.id !== authStore.user?.id"
                  class="btn btn-danger btn-sm"
                  @click="confirmDelete(user)"
                >🗑️ Dzēst</button>
                <span v-else class="self-label">Jūs</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create modal -->
    <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
      <div class="modal">
        <h3 class="modal-title">Jauns administrators</h3>

        <div v-if="formError" class="alert alert-error">{{ formError }}</div>
        <div v-if="formSuccess" class="alert alert-success">{{ formSuccess }}</div>

        <div class="form-group">
          <label>Vārds *</label>
          <input v-model="form.name" class="form-control" placeholder="Jānis Bērziņš" />
        </div>
        <div class="form-group">
          <label>E-pasts *</label>
          <input v-model="form.email" type="email" class="form-control" placeholder="j.berzins@skola.lv" />
        </div>
        <div class="form-group">
          <label>Parole * (min. 8 rakstzīmes, lielie/mazie burti, cipari)</label>
          <input v-model="form.password" type="password" class="form-control" placeholder="••••••••" />
        </div>
        <div class="form-group">
          <label>Atkārtojiet paroli *</label>
          <input v-model="form.password_confirmation" type="password" class="form-control" placeholder="••••••••" />
        </div>
        <div class="form-group">
          <label>Loma *</label>
          <select v-model="form.role" class="form-control">
            <option value="admin">admin</option>
            <option value="superadmin">superadmin</option>
          </select>
        </div>

        <div class="modal-actions">
          <button class="btn btn-secondary" @click="showModal = false">Atcelt</button>
          <button class="btn btn-primary" @click="createUser" :disabled="saving">
            {{ saving ? 'Veido...' : 'Izveidot' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete confirm -->
    <div v-if="deleteTarget" class="modal-backdrop" @click.self="deleteTarget = null">
      <div class="modal">
        <h3 class="modal-title">Dzēst lietotāju?</h3>
        <p style="color:var(--clr-text-muted)">
          Vai dzēst <strong>{{ deleteTarget.name }}</strong> ({{ deleteTarget.email }})?
          Šī darbība ir neatgriezeniska.
        </p>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="deleteTarget = null">Atcelt</button>
          <button class="btn btn-danger" @click="deleteUser" :disabled="deleting">
            {{ deleting ? 'Dzēš...' : 'Dzēst' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const users = ref([])
const loading = ref(true)
const showModal = ref(false)
const deleteTarget = ref(null)
const saving = ref(false)
const deleting = ref(false)
const formError = ref('')
const formSuccess = ref('')
const form = ref({ name: '', email: '', password: '', password_confirmation: '', role: 'admin' })

function initials(name) {
  return name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('lv-LV')
}

async function loadUsers() {
  loading.value = true
  try {
    const res = await axios.get('/admin/users')
    users.value = res.data
  } finally {
    loading.value = false
  }
}

function openModal() {
  formError.value = ''
  formSuccess.value = ''
  form.value = { name: '', email: '', password: '', password_confirmation: '', role: 'admin' }
  showModal.value = true
}

async function createUser() {
  formError.value = ''
  formSuccess.value = ''
  saving.value = true
  try {
    await axios.post('/admin/users', form.value)
    formSuccess.value = 'Lietotājs izveidots!'
    await loadUsers()
    setTimeout(() => { showModal.value = false }, 1200)
  } catch (e) {
    const errors = e.response?.data?.errors
    if (errors) {
      formError.value = Object.values(errors).flat().join(' ')
    } else {
      formError.value = e.response?.data?.message || 'Kļūda.'
    }
  } finally {
    saving.value = false
  }
}

function confirmDelete(user) { deleteTarget.value = user }

async function deleteUser() {
  deleting.value = true
  try {
    await axios.delete(`/admin/users/${deleteTarget.value.id}`)
    deleteTarget.value = null
    await loadUsers()
  } finally {
    deleting.value = false
  }
}

onMounted(loadUsers)
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; }

.users-table-wrap { overflow-x: auto; border-radius: var(--radius-lg); border: 1px solid var(--clr-border); }
.users-table { width: 100%; border-collapse: collapse; background: var(--clr-surface); }
.users-table th { padding: 0.85rem 1.25rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--clr-text-muted); background: var(--clr-surface2); border-bottom: 1px solid var(--clr-border); text-align: left; }
.users-table td { padding: 0.9rem 1.25rem; border-bottom: 1px solid var(--clr-border); font-size: 0.9rem; }
.users-table tr:last-child td { border-bottom: none; }
.users-table tr:hover td { background: var(--clr-surface2); }
.users-table tr.self td { background: var(--clr-accent-pale); }

.user-cell { display: flex; align-items: center; gap: 0.75rem; }
.avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: var(--clr-accent);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  flex-shrink: 0;
}

.self-label { font-size: 0.78rem; color: var(--clr-text-muted); font-style: italic; }
</style>
