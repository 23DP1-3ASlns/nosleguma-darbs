<template>
  <div>
    <div class="page-hero" style="background: linear-gradient(135deg, #0d3b2e 0%, #1a6b4a 100%)">
      <div class="container">
        <span class="section-label" style="color:rgba(255,255,255,0.6)">Sazinieties</span>
        <h1 class="page-title">Kontakti</h1>
      </div>
    </div>
    <div class="container" style="padding: 4rem 2rem;">
      <div class="contacts-grid">
        <div class="contact-card" v-for="item in contacts" :key="item.title">
          <div class="contact-icon">{{ item.icon }}</div>
          <h3>{{ item.title }}</h3>
          <p v-html="item.value"></p>
        </div>
      </div>

      <div class="map-placeholder">
        <div class="map-inner">
          <span>🗺️</span>
          <p>Brīvības iela 42, Rīga, LV-1010</p>
          <a href="https://maps.google.com/?q=Brīvības+iela+42,+Rīga" target="_blank" class="btn btn-primary" style="margin-top:1rem">Atvērt kartē →</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const contacts = [
  { icon: '📍', title: 'Adrese', value: 'Brīvības iela 42<br>Rīga, LV-1010' },
  { icon: '📞', title: 'Tālrunis', value: '+371 67 000 000' },
  { icon: '✉️', title: 'E-pasts', value: 'info@vidusskola.lv' },
  { icon: '🕐', title: 'Darba laiks', value: 'P–Pk: 8:00–18:00<br>Sestdiena: 9:00–13:00' },
  { icon: '📚', title: 'Bibliotēka', value: 'P–Pk: 8:00–17:00<br>Sestdiena: 10:00–14:00' },
  { icon: '🏛️', title: 'Direktors', value: 'Jānis Bērziņš<br>direktors@vidusskola.lv' },
]
</script>

<style scoped>
.page-hero { padding: 4rem 0 3rem; color: white; }
.page-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700; color: white; }

.contacts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.contact-card {
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  padding: 1.75rem;
  transition: transform var(--transition), box-shadow var(--transition);
}
.contact-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.contact-icon { font-size: 2rem; margin-bottom: 0.75rem; }
.contact-card h3 { font-family: var(--font-display); font-size: 1.1rem; margin-bottom: 0.4rem; }
.contact-card p { color: var(--clr-text-muted); line-height: 1.6; }

.map-placeholder {
  background: var(--clr-surface2);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-lg);
  height: 280px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.map-inner { text-align: center; }
.map-inner span { font-size: 3rem; display: block; margin-bottom: 1rem; }
.map-inner p { color: var(--clr-text-muted); }
</style>
