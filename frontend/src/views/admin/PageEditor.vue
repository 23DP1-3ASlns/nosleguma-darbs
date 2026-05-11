<template>
  <div class="page-editor">
    <h2 class="page-title">Lapu redaktors</h2>
    <p class="page-sub">Rediģējiet publisko lapu saturu, kas tiek rādīts vietnē.</p>

    <div class="tabs">
      <button
        v-for="tab in tabs"
        :key="tab.slug"
        class="tab"
        :class="{ active: currentSlug === tab.slug }"
        @click="switchTab(tab.slug)"
      >{{ tab.label }}</button>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else class="editor-card">
      <div v-if="saveError" class="alert alert-error">{{ saveError }}</div>
      <div v-if="saveSuccess" class="alert alert-success">{{ saveSuccess }}</div>

      <div class="form-group">
        <label>Lapas virsraksts</label>
        <input v-model="form.title" class="form-control" placeholder="Virsraksts" />
      </div>

      <div class="form-group">
        <label>Saturs (HTML atbalstīts)</label>
        <div class="html-toolbar">
          <button type="button" @click="insertTag('h2')" class="toolbar-btn">H2</button>
          <button type="button" @click="insertTag('h3')" class="toolbar-btn">H3</button>
          <button type="button" @click="insertTag('p')" class="toolbar-btn">P</button>
          <button type="button" @click="insertTag('ul')" class="toolbar-btn">UL</button>
          <button type="button" @click="insertTag('li')" class="toolbar-btn">LI</button>
          <button type="button" @click="insertTag('strong')" class="toolbar-btn">B</button>
        </div>
        <textarea
          ref="contentArea"
          v-model="form.content"
          class="form-control content-area"
          rows="16"
          placeholder="<h2>Virsraksts</h2><p>Teksts...</p>"
        ></textarea>
      </div>

      <div class="preview-section">
        <h4>Priekšskatījums</h4>
        <div class="preview-box prose" v-html="form.content"></div>
      </div>

      <div style="display:flex;gap:1rem;margin-top:1.5rem">
        <button class="btn btn-primary" @click="savePage" :disabled="saving">
          {{ saving ? 'Saglabā...' : '💾 Saglabāt izmaiņas' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tabs = [
  { slug: 'vesture', label: '🏛️ Vēsture' },
  { slug: 'skoleniem', label: '🎓 Skolēniem' },
  { slug: 'skolotajiem', label: '📚 Skolotājiem' },
]

const currentSlug = ref('vesture')
const loading = ref(false)
const saving = ref(false)
const saveError = ref('')
const saveSuccess = ref('')
const form = ref({ title: '', content: '' })
const contentArea = ref(null)

async function loadPage(slug) {
  loading.value = true
  saveError.value = ''
  saveSuccess.value = ''
  try {
    const res = await axios.get(`/pages/${slug}`)
    form.value = { title: res.data.title, content: res.data.content }
  } catch {
    form.value = { title: '', content: '' }
  } finally {
    loading.value = false
  }
}

async function switchTab(slug) {
  currentSlug.value = slug
  await loadPage(slug)
}

async function savePage() {
  saveError.value = ''
  saveSuccess.value = ''
  saving.value = true
  try {
    await axios.put(`/admin/pages/${currentSlug.value}`, form.value)
    saveSuccess.value = 'Lapa veiksmīgi saglabāta!'
    setTimeout(() => saveSuccess.value = '', 3000)
  } catch (e) {
    saveError.value = e.response?.data?.message || 'Kļūda saglabājot lapu.'
  } finally {
    saving.value = false
  }
}

function insertTag(tag) {
  const el = contentArea.value
  if (!el) return
  const start = el.selectionStart
  const end = el.selectionEnd
  const selected = form.value.content.slice(start, end)
  const wrapped = `<${tag}>${selected}</${tag}>`
  form.value.content = form.value.content.slice(0, start) + wrapped + form.value.content.slice(end)
}

onMounted(() => loadPage('vesture'))
</script>

<style scoped>
.page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
.page-sub { color: var(--clr-text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; }

.tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--clr-border); }
.tab {
  padding: 0.65rem 1.25rem;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--clr-text-muted);
  cursor: pointer;
  transition: all var(--transition);
}
.tab:hover { color: var(--clr-text); }
.tab.active { color: var(--clr-accent); border-bottom-color: var(--clr-accent); }

.editor-card {
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  padding: 1.75rem;
}

.html-toolbar { display: flex; gap: 0.4rem; margin-bottom: 0.4rem; flex-wrap: wrap; }
.toolbar-btn {
  padding: 0.3rem 0.65rem;
  background: var(--clr-surface2);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  color: var(--clr-text);
  transition: background var(--transition);
}
.toolbar-btn:hover { background: var(--clr-accent); color: white; border-color: var(--clr-accent); }

.content-area { font-family: monospace; font-size: 0.85rem; min-height: 260px; }

.preview-section { margin-top: 1.5rem; }
.preview-section h4 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--clr-text-muted); margin-bottom: 0.75rem; }
.preview-box {
  background: var(--clr-bg);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius);
  padding: 1.25rem;
  min-height: 100px;
  max-height: 300px;
  overflow-y: auto;
  line-height: 1.8;
}
.preview-box :deep(h2) { font-family: var(--font-display); font-size: 1.4rem; margin: 0.75rem 0 0.5rem; color: var(--clr-accent); }
.preview-box :deep(h3) { font-family: var(--font-display); font-size: 1.1rem; margin: 0.75rem 0 0.4rem; }
.preview-box :deep(p) { margin-bottom: 0.75rem; color: var(--clr-text-muted); }
.preview-box :deep(ul) { margin: 0.5rem 0 0.5rem 1.5rem; }
.preview-box :deep(li) { margin-bottom: 0.3rem; color: var(--clr-text-muted); }
</style>
