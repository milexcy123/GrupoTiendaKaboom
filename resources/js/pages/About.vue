<template>
  <div class="about-page">
    <v-container class="about-shell py-7 py-md-11">
      <section class="about-hero">
        <span class="eyebrow">GRUPO KABOOM EXPRESS</span>
        <h1>Un grupo, muchas soluciones para avanzar.</h1>
        <p>
          Reunimos empresas especializadas para acercarte equipos, herramientas y atención
          para tus proyectos.
        </p>
        <v-btn color="primary" to="/" append-icon="mdi-arrow-right">Conoce nuestras empresas</v-btn>
      </section>

      <section class="story-section">
        <div class="story-copy">
          <span class="eyebrow">QUIÉNES SOMOS</span>
          <h2>Soluciones pensadas para el trabajo de todos los días.</h2>
          <p>
            Grupo Kaboom Express conecta a sus clientes con productos de Bomohsa y Agrinsa:
            desde equipos para agua y energía hasta soluciones para el campo y la ganadería.
          </p>
          <p>
            Nuestro catálogo reúne distintas especialidades en un solo lugar para que encontrar
            lo que necesitas sea más sencillo.
          </p>
        </div>
        <div class="story-image" role="img" aria-label="Equipo y herramientas para distintos proyectos">
          <span class="image-stamp"><v-icon size="30">mdi-tools</v-icon> EQUIPAMOS TUS PROYECTOS</span>
        </div>
      </section>

      <section class="values-grid">
        <article class="value-card">
          <span class="value-icon"><v-icon>mdi-compass-outline</v-icon></span>
          <span class="eyebrow">MISIÓN</span>
          <h2>Hacer más fácil encontrar soluciones</h2>
          <p>Acercar productos y equipos relevantes con información clara y una experiencia de compra sencilla.</p>
        </article>
        <article class="value-card">
          <span class="value-icon"><v-icon>mdi-eye-outline</v-icon></span>
          <span class="eyebrow">VISIÓN</span>
          <h2>Crecer junto a cada proyecto</h2>
          <p>Ser un punto de encuentro confiable entre las necesidades de nuestros clientes y nuestras empresas.</p>
        </article>
        <article class="value-card value-card-dark">
          <span class="value-icon"><v-icon>mdi-handshake-outline</v-icon></span>
          <span class="eyebrow">NUESTRO COMPROMISO</span>
          <h2>Atención cercana, opciones claras</h2>
          <p>Queremos que puedas explorar, comparar y elegir con confianza.</p>
        </article>
      </section>

      <section v-if="company.list.length" class="companies-section">
        <div class="section-heading">
          <div>
            <span class="eyebrow">NUESTRAS EMPRESAS</span>
            <h2>Conoce nuestras especialidades</h2>
          </div>
        </div>
        <div class="company-grid">
          <router-link
            v-for="item in company.list"
            :key="item.id"
            :to="{ name: 'company-home', params: { slug: item.slug } }"
            class="company-link"
            :style="{ '--company-color': item.primary_color }"
          >
            <span class="company-dot" />
            <strong>{{ item.name }}</strong>
            <v-icon>mdi-arrow-up-right</v-icon>
          </router-link>
        </div>
      </section>
    </v-container>
  </div>
</template>

<script setup>
import { company } from '../stores/company';
</script>

<style scoped>
.about-page {
  min-height: 70vh;
  background: #f6f7f9;
}

.about-shell {
  max-width: 1200px;
}

.about-hero {
  padding: clamp(28px, 6vw, 60px);
  border-radius: 16px;
  color: #fff;
  background:
    radial-gradient(circle at 85% 40%, rgb(var(--v-theme-primary) / 27%), transparent 20rem),
    linear-gradient(110deg, #242222, #403a35);
}

.eyebrow {
  color: rgb(var(--v-theme-primary));
  font-size: 0.7rem;
  font-weight: 850;
  letter-spacing: 0.13em;
}

.about-hero h1 {
  max-width: 760px;
  margin: 10px 0;
  font-size: clamp(2.2rem, 6vw, 4rem);
  font-weight: 950;
  letter-spacing: -0.06em;
  line-height: 1.03;
}

.about-hero p {
  max-width: 600px;
  margin: 0 0 23px;
  color: rgb(255 255 255 / 80%);
  line-height: 1.65;
}

.story-section {
  display: grid;
  grid-template-columns: 1fr 0.9fr;
  align-items: center;
  gap: 34px;
  padding: 56px 0;
}

.story-copy h2,
.section-heading h2 {
  margin: 8px 0 14px;
  color: #292d33;
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.15;
}

.story-copy p {
  color: #6f767e;
  font-size: 0.92rem;
  line-height: 1.7;
}

.story-image {
  display: flex;
  min-height: 300px;
  align-items: flex-end;
  padding: 22px;
  border-radius: 14px;
  background:
    linear-gradient(0deg, rgb(15 23 42 / 70%), transparent 60%),
    url('https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1200&q=85') center / cover;
}

.image-stamp {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border: 1px solid rgb(255 255 255 / 28%);
  border-radius: 8px;
  color: #fff;
  background: rgb(20 24 30 / 48%);
  font-size: 0.68rem;
  font-weight: 850;
  letter-spacing: 0.08em;
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}

.value-card {
  min-height: 230px;
  padding: 22px;
  border: 1px solid #e6e8eb;
  border-radius: 12px;
  background: #fff;
}

.value-icon {
  display: grid;
  width: 44px;
  height: 44px;
  margin-bottom: 18px;
  place-items: center;
  border-radius: 12px;
  color: rgb(var(--v-theme-primary));
  background: rgb(var(--v-theme-primary) / 12%);
}

.value-card h2 {
  margin: 8px 0;
  color: #292d33;
  font-size: 1.08rem;
  font-weight: 900;
}

.value-card p {
  margin: 0;
  color: #747b83;
  font-size: 0.84rem;
  line-height: 1.6;
}

.value-card-dark {
  border-color: #292725;
  background: #292725;
}

.value-card-dark h2 {
  color: #fff;
}

.value-card-dark p {
  color: rgb(255 255 255 / 68%);
}

.companies-section {
  padding: 48px 0 12px;
}

.section-heading {
  margin-bottom: 16px;
}

.company-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.company-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border: 1px solid #e6e8eb;
  border-left: 4px solid var(--company-color);
  border-radius: 10px;
  color: #292d33;
  background: #fff;
  text-decoration: none;
}

.company-link strong {
  flex: 1;
  font-weight: 850;
}

.company-link > .v-icon {
  color: var(--company-color);
}

.company-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--company-color);
}

@media (max-width: 760px) {
  .story-section {
    grid-template-columns: 1fr;
    padding: 36px 0;
  }

  .values-grid {
    grid-template-columns: 1fr;
  }

  .company-grid {
    grid-template-columns: 1fr;
  }
}
</style>
