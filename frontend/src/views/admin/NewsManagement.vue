<template>
  <div class="news-mgmt">
    <!-- Header -->
    <div class="page-header">
      <h2 class="page-title">Jaunumu pārvaldība</h2>
      <button class="btn btn-primary" @click="openModal()">+ Jauns jaunums</button>
    </div>

    <!-- List -->
    <div v-if="loading" class="spinner"></div>
    <div v-else-if="!posts.length" class="empty-state">
      <div style="font-size:3rem">📰</div>
      <h3>Nav jaunumu</h3>
      <p>Pievienojiet pirmo jaunumu, noklikšķinot uz pogas augšā.</p>
    </div>

    <div v-else class="news-list">
      <div v-for="post in posts" :key="post.id" class="news-item">
        <div v-if="post.image_url" class="news-thumbnail">
          <img :src="post.image_url" :alt="post.title" />
        </div>
        <div v-else class="news-thumbnail-empty">📰</div>
        <div class="news-info">
          <h3 class="news-title">{{ post.title }}</h3>
          <p class="news-excerpt">{{ truncate(post.content, 100) }}</p>
          <time class="news-date">{{ formatDate(post.published_at) }}</time>
        </div>
        <div class="news-actions">
          <button class="btn btn-secondary btn-sm" @click="openModal(post)">✏️ Rediģēt</button>
          <button class="btn btn-danger btn-sm" @click="confirmDelete(post)">🗑️ Dzēst</button>
        </div>
      </div>
    </div>

    <!-- Modal: Create/Edit -->
    <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
      <div class="modal modal-lg">
        <h3 class="modal-title">{{ editPost ? 'Rediģēt jaunumu' : 'Jauns jaunums' }}</h3>

        <div v-if="formError" class="alert alert-error">{{ formError }}</div>
        <div v-if="formSuccess" class="alert alert-success">{{ formSuccess }}</div>

        <div class="form-group">
          <label>Virsraksts *</label>
          <input v-model="form.title" class="form-control" placeholder="Jaunuma virsraksts" required />
        </div>
        <div class="form-group">
          <label>Saturs *</label>
          <textarea v-model="form.content" class="form-control" rows="6" placeholder="Jaunuma teksts..."></textarea>
        </div>
        <div class="form-group">
          <label>Publicēšanas datums</label>
          <input v-model="form.published_at" type="datetime-local" class="form-control" />
        </div>
        <div class="form-group">
          <label>Attēls (neobligāts)</label>
          <input type="file" @change="handleFile" accept="image/*" class="form-control" />
          <div v-if="editPost?.image_url" class="current-image">
            <img :src="editPost.image_url" alt="Current" />
            <span>Pašreizējais attēls</span>
          </div>
        </div>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="showModal = false">Atcelt</button>
          <button class="btn btn-primary" @click="savePost" :disabled="saving">
            {{ saving ? 'Saglabā...' : 'Saglabāt' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm delete modal -->
    <div v-if="deleteTarget" class="modal-backdrop" @click.self="deleteTarget = null">
      <div class="modal">
        <h3 class="modal-title">Dzēst jaunumu?</h3>
        <p style="color: var(--clr-text-muted)">Vai tiešām vēlaties dzēst "<strong>{{ deleteTarget.title }}</strong>"? Šo darbību nevar atsaukt.</p>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="deleteTarget = null">Atcelt</button>
          <button class="btn btn-danger" @click="deletePost" :disabled="deleting">
            {{ deleting ? 'Dzēš...' : 'Dzēst' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const posts = ref([])
const loading = ref(true)
const showModal = ref(false)
const editPost = ref(null)
const deleteTarget = ref(null)
const saving = ref(false)
const deleting = ref(false)
const formError = ref('')
const formSuccess = ref('')
const selectedFile = ref(null)

const form = ref({ title: '', content: '', published_at: '' })

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('lv-LV', { year: 'numeric', month: 'long', day: 'numeric' })
}

function truncate(text, len) {
  const plain = text.replace(/<[^>]*>/g, '')
  return plain.length > len ? plain.slice(0, len) + '...' : plain
}

function openModal(post = null) {
  editPost.value = post
  formError.value = ''
  formSuccess.value = ''
  selectedFile.value = null
  if (post) {
    form.value = {
      title: post.title,
      content: post.content,
      published_at: post.published_at ? post.published_at.slice(0, 16) : '',
    }
  } else {
    form.value = { title: '', content: '', published_at: '' }
  }
  showModal.value = true
}

function handleFile(e) {
  selectedFile.value = e.target.files[0] || null
}

async function loadPosts() {
  try {
    const res = await axios.get('/news')
    posts.value = res.data
  } finally {
    loading.value = false
  }
}

async function savePost() {
  formError.value = ''
  formSuccess.value = ''
  if (!form.value.title || !form.value.content) {
    formError.value = 'Virsraksts un saturs ir obligāti lauki.'
    return
  }
  saving.value = true
  try {
    const fd = new FormData()
    fd.append('title', form.value.title)
    fd.append('content', form.value.content)
    if (form.value.published_at) fd.append('published_at', form.value.published_at)
    if (selectedFile.value) fd.append('image', selectedFile.value)

    if (editPost.value) {
      fd.append('_method', 'PUT')
      await axios.post(`/admin/news/${editPost.value.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      formSuccess.value = 'Jaunums atjaunots!'
    } else {
      await axios.post('/admin/news', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      formSuccess.value = 'Jaunums pievienots!'
    }
    await loadPosts()
    setTimeout(() => { showModal.value = false }, 1200)
  } catch (e) {
    const errors = e.response?.data?.errors
    if (errors) {
      formError.value = Object.values(errors).flat().join(' ')
    } else {
      formError.value = e.response?.data?.message || 'Kļūda saglabājot.'
    }
  } finally {
    saving.value = false
  }
}

function confirmDelete(post) {
  deleteTarget.value = post
}

async function deletePost() {
  deleting.value = true
  try {
    await axios.delete(`/admin/news/${deleteTarget.value.id}`)
    deleteTarget.value = null
    await loadPosts()
  } finally {
    deleting.value = false
  }
}

onMounted(loadPosts)
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; }

.news-list { display: flex; flex-direction: column; gap: 1rem; }

.news-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  padding: 1rem;
  transition: box-shadow var(--transition);
}
.news-item:hover { box-shadow: var(--shadow-sm); }

.news-thumbnail { width: 80px; height: 60px; border-radius: var(--radius); overflow: hidden; flex-shrink: 0; }
.news-thumbnail img { width: 100%; height: 100%; object-fit: cover; }
.news-thumbnail-empty {
  width: 80px; height: 60px;
  background: var(--clr-surface2);
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.news-info { flex: 1; min-width: 0; }
.news-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.news-excerpt { font-size: 0.8rem; color: var(--clr-text-muted); margin-bottom: 0.25rem; }
.news-date { font-size: 0.75rem; color: var(--clr-text-muted); }

.news-actions { display: flex; gap: 0.5rem; flex-shrink: 0; }

.modal-lg { max-width: 680px !important; width: 100%; }

.current-image { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
.current-image img { width: 80px; height: 60px; object-fit: cover; border-radius: var(--radius); }
.current-image span { font-size: 0.8rem; color: var(--clr-text-muted); }

.empty-state { text-align: center; padding: 4rem 0; color: var(--clr-text-muted); }
.empty-state h3 { margin: 0.75rem 0 0.4rem; font-size: 1.2rem; }

@media (max-width: 600px) {
  .news-item { flex-wrap: wrap; }
  .news-actions { width: 100%; justify-content: flex-end; }
}
</style>
