<template>
  <div class="home">
    <!-- Hero -->
    <section class="hero">
      <div class="hero-bg"></div>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <span class="hero-eyebrow fade-in">Laipni lūgti</span>
        <h1 class="hero-title fade-in fade-in-delay-1">Rīgas Vidusskola</h1>
        <p class="hero-subtitle fade-in fade-in-delay-2">Kvalitatīva izglītība, radošas iespējas, kopīga nākotne. Mūsu skola — vieta, kur aug zinātāji un līderi.</p>
        <div class="hero-actions fade-in fade-in-delay-3">
          <RouterLink to="/jaunumi" class="btn btn-hero">Skatīt jaunumus</RouterLink>
          <RouterLink to="/kontakti" class="btn btn-hero-outline">Sazināties ar mums</RouterLink>
        </div>
      </div>
      <div class="hero-scroll">
        <span>Ritiniet uz leju</span>
        <div class="scroll-indicator"></div>
      </div>
    </section>

    <!-- Intro -->
    <section class="intro section" ref="introRef">
      <div class="container">
        <div class="intro-grid" :class="{ visible: introVisible }">
          <div class="intro-text">
            <span class="section-label">Par mums</span>
            <h2 class="section-title">Izcilība izglītībā kopš 1920. gada</h2>
            <p>Mūsu skola ir ne tikai mācību iestāde — tā ir vieta, kur jaunieši atklāj sevi, attīsta talantus un gatavojas nākotnei. Ar vairāk nekā 100 gadus ilgu vēsturi, mēs lepojamies ar tradīcijām un inovatīvu pieeju mācību procesam.</p>
            <p style="margin-top: 1rem;">Katru gadu mūsu absolventi veiksmīgi turpina izglītību Latvijas un ārvalstu augstskolās, pienesot savu ieguldījumu sabiedrības attīstībā.</p>
            <RouterLink to="/vesture" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-flex;">Uzzināt vairāk →</RouterLink>
          </div>
          <div class="intro-stats">
            <div class="stat-card">
              <div class="stat-number">100+</div>
              <div class="stat-label">Gadu vēsture</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">800+</div>
              <div class="stat-label">Skolēni</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">60+</div>
              <div class="stat-label">Pedagogi</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">95%</div>
              <div class="stat-label">Absolventi augstskolā</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick links -->
    <section class="quick-links section">
      <div class="container">
        <h2 class="section-title text-center">Ātrās saites</h2>
        <p class="section-subtitle text-center">Viss, kas jums nepieciešams, vienā vietā</p>
        <div class="cards-grid">
          <RouterLink v-for="card in quickLinks" :key="card.to" :to="card.to" class="quick-card">
            <div class="quick-card-icon">{{ card.icon }}</div>
            <h3 class="quick-card-title">{{ card.title }}</h3>
            <p class="quick-card-desc">{{ card.desc }}</p>
            <span class="quick-card-arrow">→</span>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- News preview -->
    <section class="news-preview section">
      <div class="container">
        <div class="section-header">
          <div>
            <span class="section-label">Aktualitātes</span>
            <h2 class="section-title">Jaunākie jaunumi</h2>
          </div>
          <RouterLink to="/jaunumi" class="btn btn-secondary">Visi jaunumi →</RouterLink>
        </div>
        <div v-if="loadingNews" class="spinner"></div>
        <div v-else class="news-grid">
          <article v-for="post in latestNews" :key="post.id" class="news-card card">
            <div v-if="post.image_url" class="news-card-img">
              <img :src="post.image_url" :alt="post.title" />
            </div>
            <div v-else class="news-card-img-placeholder">📰</div>
            <div class="card-body">
              <time class="news-date">{{ formatDate(post.published_at) }}</time>
              <h3 class="news-title">{{ post.title }}</h3>
              <p class="news-excerpt">{{ truncate(post.content, 120) }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'

const latestNews = ref([])
const loadingNews = ref(true)
const introVisible = ref(false)
const introRef = ref(null)

const quickLinks = [
  { to: '/jaunumi', icon: '📰', title: 'Jaunumi', desc: 'Pēdējās ziņas un notikumi skolā' },
  { to: '/stundu-saraksts', icon: '📅', title: 'Stundu saraksts', desc: 'Meklē savu klasi un stundu laiku' },
  { to: '/skoleniem', icon: '🎓', title: 'Skolēniem', desc: 'Noderīga informācija skolēniem' },
  { to: '/skolotajiem', icon: '📚', title: 'Skolotājiem', desc: 'Resursi un informācija pedagogiem' },
  { to: '/vesture', icon: '🏛️', title: 'Vēsture', desc: 'Skolas vēsture kopš 1920. gada' },
  { to: '/kontakti', icon: '📞', title: 'Kontakti', desc: 'Sazinies ar skolu' },
]

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('lv-LV', { year: 'numeric', month: 'long', day: 'numeric' })
}

function truncate(text, len) {
  const plain = text.replace(/<[^>]*>/g, '')
  return plain.length > len ? plain.slice(0, len) + '...' : plain
}

onMounted(async () => {
  try {
    const res = await axios.get('/news')
    latestNews.value = res.data.slice(0, 3)
  } catch {
    latestNews.value = []
  } finally {
    loadingNews.value = false
  }

  // Intersection observer for scroll animation
  const observer = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) introVisible.value = true
  }, { threshold: 0.1 })
  if (introRef.value) observer.observe(introRef.value)
})
</script>

<style scoped>
/* Hero */
.hero {
  position: relative;
  min-height: 90vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1a4a2e 0%, #0d2b1a 40%, #2d7a4e 100%);
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 800px;
  padding: 0 2rem;
}

.hero-eyebrow {
  display: inline-block;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--clr-gold);
  margin-bottom: 1rem;
}

.hero-title {
  font-family: var(--font-display);
  font-size: clamp(2.5rem, 7vw, 5rem);
  font-weight: 700;
  color: #fff;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
}

.hero-subtitle {
  font-size: clamp(1rem, 2vw, 1.2rem);
  color: rgba(255,255,255,0.8);
  max-width: 580px;
  margin: 0 auto 2rem;
  line-height: 1.7;
}

.hero-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

.btn-hero {
  background: var(--clr-gold);
  color: #1a1200;
  font-weight: 600;
  padding: 0.85rem 2rem;
  border-radius: var(--radius);
  font-size: 1rem;
  transition: all var(--transition);
  border: none;
}
.btn-hero:hover { background: #e0c560; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(201,168,76,0.4); }

.btn-hero-outline {
  background: transparent;
  color: #fff;
  font-weight: 600;
  padding: 0.85rem 2rem;
  border-radius: var(--radius);
  font-size: 1rem;
  border: 2px solid rgba(255,255,255,0.4);
  transition: all var(--transition);
}
.btn-hero-outline:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.7); }

.hero-scroll {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255,255,255,0.5);
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

.scroll-indicator {
  width: 1px;
  height: 40px;
  background: linear-gradient(to bottom, rgba(255,255,255,0.4), transparent);
  animation: scrollPulse 2s ease infinite;
}
@keyframes scrollPulse {
  0%, 100% { opacity: 0.4; }
  50% { opacity: 1; }
}

/* Section styles */
.section { padding: 5rem 0; }

.section-label {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--clr-accent);
  margin-bottom: 0.75rem;
}

.section-title {
  font-family: var(--font-display);
  font-size: clamp(1.6rem, 3.5vw, 2.4rem);
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.section-subtitle {
  color: var(--clr-text-muted);
  margin-bottom: 3rem;
  font-size: 1.05rem;
}

.text-center { text-align: center; }

.section-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

/* Intro section */
.intro { background: var(--clr-surface); }
.intro-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.7s ease;
}
.intro-grid.visible { opacity: 1; transform: translateY(0); }

.intro-text p { color: var(--clr-text-muted); line-height: 1.8; }

.intro-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.stat-card {
  background: var(--clr-bg);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  text-align: center;
  transition: transform var(--transition);
}
.stat-card:hover { transform: translateY(-3px); }
.stat-number {
  font-family: var(--font-display);
  font-size: 2.2rem;
  font-weight: 700;
  color: var(--clr-accent);
  line-height: 1;
  margin-bottom: 0.4rem;
}
.stat-label { font-size: 0.82rem; color: var(--clr-text-muted); }

/* Quick links */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.quick-card {
  display: flex;
  flex-direction: column;
  padding: 1.75rem;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  text-decoration: none;
  color: var(--clr-text);
  transition: all var(--transition);
  position: relative;
  overflow: hidden;
}
.quick-card::before {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
  background: var(--clr-accent);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.35s ease;
}
.quick-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: var(--clr-accent); color: var(--clr-text); }
.quick-card:hover::before { transform: scaleX(1); }

.quick-card-icon { font-size: 2.5rem; margin-bottom: 1rem; }
.quick-card-title { font-family: var(--font-display); font-size: 1.15rem; font-weight: 600; margin-bottom: 0.5rem; }
.quick-card-desc { font-size: 0.875rem; color: var(--clr-text-muted); flex: 1; line-height: 1.6; }
.quick-card-arrow { font-size: 1.1rem; color: var(--clr-accent); margin-top: 1rem; transition: transform var(--transition); }
.quick-card:hover .quick-card-arrow { transform: translateX(4px); }

/* News preview */
.news-preview { background: var(--clr-surface2); }
.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}
.news-card { cursor: default; }
.news-card-img { height: 180px; overflow: hidden; }
.news-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.news-card:hover .news-card-img img { transform: scale(1.05); }
.news-card-img-placeholder {
  height: 180px;
  background: var(--clr-accent-pale);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
}
.news-date { font-size: 0.78rem; color: var(--clr-text-muted); margin-bottom: 0.5rem; display: block; }
.news-title { font-family: var(--font-display); font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; line-height: 1.3; }
.news-excerpt { font-size: 0.875rem; color: var(--clr-text-muted); line-height: 1.6; }

@media (max-width: 768px) {
  .intro-grid { grid-template-columns: 1fr; gap: 2rem; }
  .intro-stats { grid-template-columns: 1fr 1fr; }
  .hero { min-height: 80vh; }
}
</style>
