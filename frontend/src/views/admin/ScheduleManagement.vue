<template>
  <div class="schedule-mgmt">
    <div class="page-header">
      <h2 class="page-title">Stundu saraksts</h2>
      <button class="btn btn-primary" @click="openModal()">+ Pievienot stundu</button>
    </div>

    <!-- Group filter -->
    <div class="filter-row">
      <select v-model="filterGroup" class="form-control filter-select" @change="loadSchedule">
        <option value="">Visas klases</option>
        <option v-for="g in groups" :key="g" :value="g">{{ g }}</option>
      </select>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else-if="!schedule.length" class="empty-state">
      <div style="font-size:3rem">📅</div>
      <h3>Nav stundu ierakstu</h3>
    </div>

    <div v-else>
      <div v-for="day in days" :key="day" v-show="byDay[day]?.length">
        <h3 class="day-heading">{{ day }}</h3>
        <div class="schedule-table-wrap">
          <table class="schedule-table">
            <thead>
              <tr>
                <th>Laiks</th>
                <th>Priekšmets</th>
                <th>Skolotājs</th>
                <th>Klase</th>
                <th>Darbības</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="entry in byDay[day]" :key="entry.id" :class="{ conflict: conflictId === entry.id }">
                <td><span class="time-badge">{{ entry.start_time.slice(0,5) }} – {{ entry.end_time.slice(0,5) }}</span></td>
                <td><strong>{{ entry.subject }}</strong></td>
                <td>{{ entry.teacher }}</td>
                <td><span class="badge badge-green">{{ entry.group_name }}</span></td>
                <td>
                  <div style="display:flex;gap:0.4rem">
                    <button class="btn btn-secondary btn-sm" @click="openModal(entry)">✏️</button>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(entry)">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
      <div class="modal">
        <h3 class="modal-title">{{ editEntry ? 'Rediģēt stundu' : 'Pievienot stundu' }}</h3>

        <div v-if="formError" class="alert alert-error">⚠️ {{ formError }}</div>
        <div v-if="formSuccess" class="alert alert-success">✅ {{ formSuccess }}</div>

        <div class="form-row">
          <div class="form-group">
            <label>Klase *</label>
            <input v-model="form.group_name" class="form-control" placeholder="piem. 10.A" />
          </div>
          <div class="form-group">
            <label>Priekšmets *</label>
            <input v-model="form.subject" class="form-control" placeholder="piem. Matemātika" />
          </div>
        </div>
        <div class="form-group">
          <label>Skolotājs *</label>
          <input v-model="form.teacher" class="form-control" placeholder="piem. A. Bērziņš" />
        </div>
        <div class="form-group">
          <label>Diena *</label>
          <select v-model="form.day_of_week" class="form-control">
            <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Sākums *</label>
            <input v-model="form.start_time" type="time" class="form-control" />
          </div>
          <div class="form-group">
            <label>Beigas *</label>
            <input v-model="form.end_time" type="time" class="form-control" />
          </div>
        </div>

        <div class="modal-actions">
          <button class="btn btn-secondary" @click="showModal = false">Atcelt</button>
          <button class="btn btn-primary" @click="saveEntry" :disabled="saving">
            {{ saving ? 'Saglabā...' : 'Saglabāt' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm delete -->
    <div v-if="deleteTarget" class="modal-backdrop" @click.self="deleteTarget = null">
      <div class="modal">
        <h3 class="modal-title">Dzēst stundu?</h3>
        <p style="color:var(--clr-text-muted)">Vai dzēst <strong>{{ deleteTarget.subject }}</strong> ({{ deleteTarget.group_name }}, {{ deleteTarget.day_of_week }})?</p>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="deleteTarget = null">Atcelt</button>
          <button class="btn btn-danger" @click="deleteEntry" :disabled="deleting">
            {{ deleting ? 'Dzēš...' : 'Dzēst' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const schedule = ref([])
const groups = ref([])
const loading = ref(true)
const showModal = ref(false)
const editEntry = ref(null)
const deleteTarget = ref(null)
const saving = ref(false)
const deleting = ref(false)
const formError = ref('')
const formSuccess = ref('')
const conflictId = ref(null)
const filterGroup = ref('')

const days = ['Pirmdiena', 'Otrdiena', 'Trešdiena', 'Ceturtdiena', 'Piektdiena']
const form = ref({ group_name: '', subject: '', teacher: '', day_of_week: 'Pirmdiena', start_time: '08:00', end_time: '08:45' })

const byDay = computed(() => {
  const map = {}
  for (const d of days) {
    map[d] = schedule.value.filter(e => e.day_of_week === d)
  }
  return map
})

async function loadSchedule() {
  loading.value = true
  try {
    const params = filterGroup.value ? { group: filterGroup.value } : {}
    const res = await axios.get('/schedule', { params })
    schedule.value = res.data
  } finally {
    loading.value = false
  }
}

function openModal(entry = null) {
  editEntry.value = entry
  formError.value = ''
  formSuccess.value = ''
  conflictId.value = null
  if (entry) {
    form.value = { ...entry }
  } else {
    form.value = { group_name: '', subject: '', teacher: '', day_of_week: 'Pirmdiena', start_time: '08:00', end_time: '08:45' }
  }
  showModal.value = true
}

async function saveEntry() {
  formError.value = ''
  formSuccess.value = ''
  conflictId.value = null
  saving.value = true
  try {
    if (editEntry.value) {
      await axios.put(`/admin/schedule/${editEntry.value.id}`, form.value)
    } else {
      await axios.post('/admin/schedule', form.value)
    }
    formSuccess.value = 'Saglabāts!'
    await loadSchedule()
    // Refresh groups
    const gr = await axios.get('/schedule/groups')
    groups.value = gr.data
    setTimeout(() => { showModal.value = false }, 1000)
  } catch (e) {
    const errors = e.response?.data?.errors
    const conflict = e.response?.data?.conflict
    if (conflict) conflictId.value = conflict.id
    if (errors) {
      formError.value = Object.values(errors).flat().join(' ')
    } else {
      formError.value = e.response?.data?.message || 'Kļūda saglabājot.'
    }
  } finally {
    saving.value = false
  }
}

function confirmDelete(entry) { deleteTarget.value = entry }

async function deleteEntry() {
  deleting.value = true
  try {
    await axios.delete(`/admin/schedule/${deleteTarget.value.id}`)
    deleteTarget.value = null
    await loadSchedule()
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  const [, gr] = await Promise.all([loadSchedule(), axios.get('/schedule/groups')])
  groups.value = gr.data
})
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; }

.filter-row { margin-bottom: 1.5rem; }
.filter-select { max-width: 220px; }

.day-heading {
  font-family: var(--font-display);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--clr-accent);
  margin: 1.5rem 0 0.75rem;
  padding-bottom: 0.4rem;
  border-bottom: 2px solid var(--clr-accent-pale);
}

.schedule-table-wrap { overflow-x: auto; border-radius: var(--radius-lg); border: 1px solid var(--clr-border); margin-bottom: 0.5rem; }
.schedule-table { width: 100%; border-collapse: collapse; background: var(--clr-surface); }
.schedule-table th { padding: 0.75rem 1rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--clr-text-muted); background: var(--clr-surface2); border-bottom: 1px solid var(--clr-border); text-align: left; }
.schedule-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--clr-border); font-size: 0.88rem; }
.schedule-table tr:last-child td { border-bottom: none; }
.schedule-table tr.conflict td { background: #fdecea; }
[data-theme="dark"] .schedule-table tr.conflict td { background: #2d1b1b; }

.time-badge { background: var(--clr-accent-pale); color: var(--clr-accent); padding: 0.2rem 0.5rem; border-radius: 99px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.empty-state { text-align: center; padding: 3rem 0; color: var(--clr-text-muted); }
.empty-state h3 { margin-top: 0.75rem; }

@media (max-width: 500px) {
  .form-row { grid-template-columns: 1fr; }
}
</style>
