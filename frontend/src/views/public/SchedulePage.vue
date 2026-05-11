<template>
  <div>
    <div class="page-hero">
      <div class="container">
        <span class="section-label" style="color:rgba(255,255,255,0.6)">Mācību plāns</span>
        <h1 class="page-title">Stundu saraksts</h1>
      </div>
    </div>

    <div class="container" style="padding: 3rem 2rem;">
      <!-- Filter -->
      <div class="filter-bar">
        <label class="filter-label">Filtrēt pēc klases:</label>
        <div class="filter-chips">
          <button
            class="chip"
            :class="{ active: selectedGroup === '' }"
            @click="selectedGroup = ''; loadSchedule()"
          >Visas klases</button>
          <button
            v-for="group in groups"
            :key="group"
            class="chip"
            :class="{ active: selectedGroup === group }"
            @click="selectedGroup = group; loadSchedule()"
          >{{ group }}</button>
        </div>
      </div>

      <div v-if="loading" class="spinner"></div>

      <div v-else-if="!schedule.length" class="empty-state">
        <div class="empty-icon">📅</div>
        <h3>Nav ierakstu</h3>
        <p v-if="selectedGroup">Klasei "{{ selectedGroup }}" nav pievienots stundu saraksts.</p>
        <p v-else>Stundu saraksts vēl nav pievienots.</p>
      </div>

      <div v-else>
        <div v-for="day in days" :key="day" class="day-block" v-show="scheduleByDay[day]?.length">
          <h2 class="day-title">{{ day }}</h2>
          <div class="schedule-table-wrap">
            <table class="schedule-table">
              <thead>
                <tr>
                  <th>Laiks</th>
                  <th>Priekšmets</th>
                  <th>Skolotājs</th>
                  <th v-if="!selectedGroup">Klase</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="entry in scheduleByDay[day]" :key="entry.id">
                  <td class="time-cell">
                    <span class="time-badge">{{ entry.start_time.slice(0,5) }} – {{ entry.end_time.slice(0,5) }}</span>
                  </td>
                  <td class="subject-cell"><strong>{{ entry.subject }}</strong></td>
                  <td>{{ entry.teacher }}</td>
                  <td v-if="!selectedGroup"><span class="badge badge-green">{{ entry.group_name }}</span></td>
                </tr>
              </tbody>
            </table>
          </div>
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
const selectedGroup = ref('')

const days = ['Pirmdiena', 'Otrdiena', 'Trešdiena', 'Ceturtdiena', 'Piektdiena']

const scheduleByDay = computed(() => {
  const map = {}
  for (const day of days) {
    map[day] = schedule.value.filter(e => e.day_of_week === day)
  }
  return map
})

async function loadSchedule() {
  loading.value = true
  try {
    const params = selectedGroup.value ? { group: selectedGroup.value } : {}
    const res = await axios.get('/schedule', { params })
    schedule.value = res.data
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const [, groupsRes] = await Promise.all([
    loadSchedule(),
    axios.get('/schedule/groups')
  ])
  groups.value = groupsRes.data
})
</script>

<style scoped>
.page-hero {
  background: linear-gradient(135deg, #1a3a4a 0%, #2d6a8e 100%);
  padding: 4rem 0 3rem;
  color: white;
}
.page-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700; color: white; }

.filter-bar { margin-bottom: 2rem; }
.filter-label { font-weight: 600; font-size: 0.875rem; color: var(--clr-text-muted); margin-bottom: 0.75rem; display: block; }
.filter-chips { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.chip {
  padding: 0.4rem 1rem;
  border: 1.5px solid var(--clr-border);
  border-radius: 99px;
  background: var(--clr-surface);
  color: var(--clr-text);
  font-size: 0.875rem;
  cursor: pointer;
  transition: all var(--transition);
}
.chip:hover { border-color: var(--clr-accent); color: var(--clr-accent); }
.chip.active { background: var(--clr-accent); color: white; border-color: var(--clr-accent); }

.day-block { margin-bottom: 2.5rem; }
.day-title {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--clr-accent);
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--clr-accent-pale);
}

.schedule-table-wrap { overflow-x: auto; border-radius: var(--radius-lg); border: 1px solid var(--clr-border); }
.schedule-table { width: 100%; border-collapse: collapse; background: var(--clr-surface); }
.schedule-table th {
  padding: 0.9rem 1.25rem;
  text-align: left;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--clr-text-muted);
  background: var(--clr-surface2);
  border-bottom: 1px solid var(--clr-border);
}
.schedule-table td {
  padding: 0.9rem 1.25rem;
  border-bottom: 1px solid var(--clr-border);
  font-size: 0.92rem;
}
.schedule-table tr:last-child td { border-bottom: none; }
.schedule-table tr:hover td { background: var(--clr-surface2); }

.time-badge {
  background: var(--clr-accent-pale);
  color: var(--clr-accent);
  padding: 0.25rem 0.6rem;
  border-radius: 99px;
  font-size: 0.82rem;
  font-weight: 600;
  white-space: nowrap;
}

.subject-cell strong { font-weight: 600; }

.empty-state { text-align: center; padding: 4rem 0; color: var(--clr-text-muted); }
.empty-icon { font-size: 3.5rem; margin-bottom: 1rem; }
.empty-state h3 { font-size: 1.2rem; margin-bottom: 0.5rem; }
</style>
