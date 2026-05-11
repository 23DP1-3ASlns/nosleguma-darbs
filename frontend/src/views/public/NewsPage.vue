<template>
  <div class="news-page">
    <div class="page-hero">
      <div class="container">
        <span class="section-label">Aktualitātes</span>
        <h1 class="page-title">Jaunumi</h1>
        <p class="page-subtitle">Pēdējās ziņas un notikumi mūsu skolā</p>
      </div>
    </div>

    <div class="container" style="padding: 4rem 2rem;">
      <div v-if="loading" class="spinner"></div>

      <div v-else-if="!posts.length" class="empty-state">
        <div class="empty-icon">📰</div>
        <h3>Pagaidām nav jaunumu</h3>
        <p>Pārbaudiet vēlāk!</p>
      </div>

      <div v-else class="news-grid">
        <article
          v-for="(post, i) in posts"
          :key="post.id"
          class="news-article card fade-in"
          :style="`animation-delay: ${i * 0.08}s`"
        >
          <div v-if="post.image_url" class="article-img">
            <img :src="post.image_url" :alt="post.title" />
          </div>
          <div v-else class="article-img-placeholder">📰</div>
          <div class="article-body">
            <div class="article-meta">
              <time>{{ formatDate(post.published_at) }}</time>
            </div>
            <h2 class="article-title">{{ post.title }}</h2>
            <div class="article-content" v-html="post.content"></div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const posts = ref([])
const loading = ref(true)

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('lv-LV', { year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(async () => {
  try {
    const res = await axios.get('/news')
    posts.value = res.data
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page-hero {
  background: linear-gradient(135deg, var(--clr-accent) 0%, var(--clr-accent-light) 100%);
  padding: 4rem 0 3rem;
  color: white;
}
.page-hero .section-label { color: rgba(255,255,255,0.7); }
.page-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700; color: white; margin-bottom: 0.75rem; }
.page-subtitle { color: rgba(255,255,255,0.8); font-size: 1.05rem; }

.news-grid { display: flex; flex-direction: column; gap: 2rem; max-width: 860px; margin: 0 auto; }

.news-article {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 0;
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.article-img { height: 240px; overflow: hidden; }
.article-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.news-article:hover .article-img img { transform: scale(1.03); }

.article-img-placeholder {
  height: 240px;
  background: var(--clr-accent-pale);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3.5rem;
}

.article-body { padding: 2rem; }

.article-meta { margin-bottom: 0.75rem; }
.article-meta time { font-size: 0.8rem; color: var(--clr-text-muted); }

.article-title {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.3;
}

.article-content {
  color: var(--clr-text-muted);
  line-height: 1.75;
  font-size: 0.95rem;
}

.empty-state { text-align: center; padding: 4rem 0; color: var(--clr-text-muted); }
.empty-icon { font-size: 4rem; margin-bottom: 1rem; }
.empty-state h3 { font-size: 1.3rem; margin-bottom: 0.5rem; }

@media (max-width: 700px) {
  .news-article { grid-template-columns: 1fr; }
  .article-img { height: 200px; }
  .article-img-placeholder { height: 200px; }
}
</style>
