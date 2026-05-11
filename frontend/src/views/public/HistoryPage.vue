<template>
  <div class="page-wrap">
    <div class="page-hero" style="background: linear-gradient(135deg, #2c1810 0%, #5a3520 100%)">
      <div class="container">
        <span class="section-label" style="color: rgba(255,255,255,0.6)">Par skolu</span>
        <h1 class="page-title">{{ page?.title || 'Vēsture' }}</h1>
      </div>
    </div>
    <div class="container content-container">
      <div v-if="loading" class="spinner"></div>
      <div v-else class="page-content prose" v-html="page?.content"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
const page = ref(null)
const loading = ref(true)
onMounted(async () => {
  try { const res = await axios.get('/pages/vesture'); page.value = res.data }
  finally { loading.value = false }
})
</script>

<style scoped>
.page-hero { padding: 4rem 0 3rem; color: white; }
.page-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700; color: white; }
.content-container { padding: 4rem 2rem; max-width: 860px; }
.prose { line-height: 1.8; color: var(--clr-text); }
.prose :deep(h2) { font-family: var(--font-display); font-size: 1.8rem; margin: 2rem 0 1rem; color: var(--clr-accent); }
.prose :deep(h3) { font-family: var(--font-display); font-size: 1.3rem; margin: 1.5rem 0 0.75rem; }
.prose :deep(p) { margin-bottom: 1rem; color: var(--clr-text-muted); }
.prose :deep(ul) { margin: 1rem 0 1rem 1.5rem; }
.prose :deep(li) { margin-bottom: 0.4rem; color: var(--clr-text-muted); }
</style>
