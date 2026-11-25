<template>
  <div class="home-page">
    <v-container class="storefront-shell py-5 py-md-7">
      <section class="hero-grid" aria-label="Promociones destacadas">
        <div class="hero-main">
          <v-carousel
            v-if="banners.length"
            class="hero-carousel"
            height="330"
            cycle
            hide-delimiter-background
            show-arrows="hover"
            aria-label="Promociones"
          >
            <v-carousel-item v-for="banner in banners" :key="banner.id">
              <div class="hero-slide" :style="bannerStyle(banner)">
                <div class="hero-copy">
                  <span class="hero-kicker">HERRAMIENTAS PARA TUS PROYECTOS</span>
                  <h1>{{ banner.title }}</h1>
                  <p>Encuentra el equipo que necesitas para trabajar mejor.</p>
                  <v-btn color="primary" to="/catalogo" append-icon="mdi-arrow-right">Explorar productos</v-btn>
                </div>
              </div>
            </v-carousel-item>
          </v-carousel>
          <div v-else class="hero-slide hero-fallback">
            <div class="hero-copy">
              <span class="hero-kicker">GRUPO KABOOM EXPRESS</span>
              <h1>Herramientas para cada proyecto.</h1>
              <p>Equípate para el trabajo, el taller y el hogar.</p>
              <v-btn color="primary" to="/catalogo" append-icon="mdi-arrow-right">Explorar productos</v-btn>
            </div>
            <v-icon class="hero-tool" size="180" aria-hidden="true">mdi-tools</v-icon>
          </div>
        </div>

        <aside class="hero-deals" aria-label="Ofertas rápidas">
          <button
            v-for="product in offers.slice(0, 2)"
            :key="product.id"
            class="side-offer"
            type="button"
            @click="verProducto(product.id)"
          >
            <div class="side-offer-image">
              <v-img v-if="product.image" :src="product.image" :alt="product.name" cover />
              <v-icon v-else size="54">mdi-tools</v-icon>
              <span class="side-discount">-{{ product.offers[0].discount }}%</span>
            </div>
            <div class="side-offer-copy">
              <span>OFERTA ESPECIAL</span>
              <strong>{{ product.name }}</strong>
              <b>{{ formatMoney(productPrice(product).final) }}</b>
            </div>
          </button>
          <div v-if="!offers.length && !cargando" class="side-offer-empty">
            <v-icon size="38">mdi-tag-outline</v-icon>
            <span>Muy pronto, nuevas ofertas</span>
          </div>
        </aside>
      </section>

      <section v-if="company.list.length" class="company-hub">
        <div class="company-hub-heading">
          <div>
            <span class="section-kicker">GRUPO KABOOM EXPRESS</span>
            <h2>¿Qué necesitas para tu proyecto?</h2>
            <p>Elige tu empresa y encuentra soluciones hechas para ti.</p>
          </div>
          <v-icon class="company-hub-icon" size="42">mdi-arrow-down-right</v-icon>
        </div>
        <div class="company-card-grid">
          <button
            v-for="item in company.list"
            :key="item.id"
            type="button"
            class="company-card"
            :style="{ '--company-color': item.primary_color || '#1565C0' }"
            @click="verEmpresa(item.slug)"
          >
            <div class="company-card-image" :style="{ backgroundImage: `linear-gradient(90deg, rgba(15, 23, 42, .78), rgba(15, 23, 42, .08)), url('${companyVisual(item.slug).image}')` }">
              <span class="company-logo-mark">{{ companyVisual(item.slug).mark }}</span>
              <span class="company-card-name">{{ item.name }}</span>
              <span class="company-card-tagline">{{ companyVisual(item.slug).tagline }}</span>
            </div>
            <span class="company-card-footer">
              <span>Conoce nuestros productos</span>
              <v-icon size="20">mdi-arrow-up-right</v-icon>
            </span>
          </button>
        </div>
      </section>

      <section v-if="categories.length" class="category-section">
        <div class="category-heading">
          <h2><v-icon color="primary">mdi-view-grid</v-icon> Compra por categoría</h2>
          <router-link to="/catalogo">Ver todas <v-icon size="16">mdi-arrow-right</v-icon></router-link>
        </div>
        <div class="category-strip">
          <button
            v-for="(category, index) in categories.slice(0, 6)"
            :key="category.id"
            type="button"
            class="category-tile"
            @click="verCategoria(category.id)"
          >
            <span class="category-icon">
              <v-icon>{{ categoryIcons[index % categoryIcons.length] }}</v-icon>
            </span>
            <span>{{ category.name }}</span>
          </button>
        </div>
      </section>

      <section class="sale-section">
        <div class="section-title">
          <div>
            <span class="section-kicker">NO TE LO PIERDAS</span>
            <h2>Productos en oferta</h2>
          </div>
          <v-btn to="/ofertas" variant="text" append-icon="mdi-arrow-right">Ver ofertas</v-btn>
        </div>
        <v-alert v-if="error" class="mb-4" type="error" variant="tonal">{{ error }}</v-alert>
        <v-progress-linear v-if="cargando" color="primary" indeterminate rounded />
        <div v-else-if="offers.length" class="sale-layout">
          <div class="sale-promo">
            <span class="sale-stamp">PRECIO<br />ESPECIAL</span>
            <v-icon class="sale-tool" size="94">mdi-tag-multiple</v-icon>
            <span class="sale-copy">Aprovecha las ofertas vigentes</span>
            <strong>Hasta {{ maxDiscount }}% OFF</strong>
          </div>
          <div class="sale-products">
            <ProductCard v-for="product in offers.slice(0, 3)" :key="product.id" :product="product" />
          </div>
        </div>
        <div v-else class="empty-state">No hay ofertas activas en este momento.</div>
      </section>

      <section v-if="latest.length" class="featured-section">
        <div class="featured-heading">
          <div>
            <span class="section-kicker">SELECCIÓN KABOOM</span>
            <h2>Novedades para tu taller</h2>
          </div>
          <v-btn to="/catalogo" variant="text" append-icon="mdi-arrow-right">Ver catálogo</v-btn>
        </div>
        <div class="featured-products">
          <ProductCard v-for="product in latest.slice(0, 5)" :key="product.id" :product="product" />
        </div>
      </section>

      <section v-if="brands.length" class="brand-strip" aria-label="Marcas disponibles">
        <span class="brand-label">MARCAS DISPONIBLES</span>
        <div class="brand-list">
          <span v-for="brand in brands" :key="brand">{{ brand }}</span>
        </div>
      </section>

      <section v-for="(category, index) in categoryShowcases" :key="category.id" class="category-showcase">
        <div class="showcase-banner" :class="{ 'showcase-banner-alt': index % 2 }">
          <div>
            <span class="section-kicker">EXPLORA NUESTRA SELECCIÓN</span>
            <h2>{{ category.name }}</h2>
          </div>
          <v-btn color="primary" variant="flat" @click="verCategoria(category.id)">Ver productos</v-btn>
        </div>
        <div class="showcase-products">
          <ProductCard v-for="product in category.products" :key="product.id" :product="product" />
        </div>
      </section>

    </v-container>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import api, { errorMessage } from '../services/api';
import { formatMoney, productPrice } from '../services/format';
import { company, selectCompany } from '../stores/company';

const router = useRouter();
const banners = ref([]);
const categories = ref([]);
const offers = ref([]);
const latest = ref([]);
const products = ref([]);
const cargando = ref(true);
const error = ref('');
const categoryIcons = [
  'mdi-tools',
  'mdi-hammer',
  'mdi-screwdriver',
  'mdi-battery-charging',
  'mdi-hard-hat',
  'mdi-lightning-bolt',
];

const maxDiscount = computed(() =>
  Math.max(...offers.value.map((product) => Number(product.offers[0].discount)), 0)
);
const brands = computed(() =>
  [...new Set(products.value.map((product) => product.brand).filter(Boolean))].slice(0, 5)
);
const categoryShowcases = computed(() =>
  categories.value
    .map((category) => ({
      ...category,
      products: products.value.filter((product) => product.category_id === category.id).slice(0, 4),
    }))
    .filter((category) => category.products.length)
    .slice(0, 2)
);

function bannerStyle(banner) {
  const overlay = 'linear-gradient(90deg, rgba(28, 27, 25, .88) 0%, rgba(28, 27, 25, .48) 58%, rgba(28, 27, 25, .04) 100%)';

  return {
    backgroundImage: banner.image
      ? `${overlay}, url("${banner.image}")`
      : 'linear-gradient(110deg, #292725 0%, #47402d 68%, #bd9200 150%)',
  };
}

function verCategoria(categoryId) {
  router.push({ name: 'catalog', query: { category_id: categoryId } });
}

function verProducto(productId) {
  router.push(`/producto/${productId}`);
}

function verEmpresa(slug) {
  selectCompany(slug);
  router.push({ name: 'company-home', params: { slug } });
}

function companyVisual(slug) {
  const visuals = {
    bomohsa: {
      mark: 'B',
      tagline: 'Agua, energía y soluciones industriales',
      image: 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1200&q=85',
    },
    agrinsa: {
      mark: 'A',
      tagline: 'Tecnología para el campo y la ganadería',
      image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=85',
    },
  };

  return visuals[slug] || {
    mark: slug.slice(0, 1).toUpperCase(),
    tagline: 'Soluciones para tus proyectos',
    image: 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1200&q=85',
  };
}

onMounted(async () => {
  selectCompany('all');

  try {
    const [bannerResponse, offerResponse, latestResponse, categoryResponse, productResponse] = await Promise.all([
      api.get('/banners'),
      api.get('/offers'),
      api.get('/products-latest'),
      api.get('/categories'),
      api.get('/products'),
    ]);

    banners.value = bannerResponse.data;
    offers.value = offerResponse.data.map((offer) => ({
      ...offer.product,
      offers: [{ discount: offer.discount }],
    }));
    latest.value = latestResponse.data;
    categories.value = categoryResponse.data;
    products.value = productResponse.data;
  } catch (requestError) {
    error.value = errorMessage(requestError);
  } finally {
    cargando.value = false;
  }
});
</script>

<style scoped>
.home-page {
  min-height: 100%;
  background: #f5f5f4;
}

.storefront-shell {
  max-width: 1280px;
}

.hero-grid {
  display: grid;
  grid-template-columns: minmax(0, 2.2fr) minmax(235px, 0.8fr);
  gap: 14px;
  margin-bottom: 22px;
}

.hero-main,
.hero-carousel {
  overflow: hidden;
  border-radius: 7px;
}

.hero-slide {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  min-height: 300px;
  padding: clamp(24px, 5vw, 58px);
  color: #fff;
  background-position: center;
  background-size: cover;
}

.hero-fallback {
  min-height: 330px;
  background:
    radial-gradient(circle at 79% 50%, rgb(var(--v-theme-primary) / 35%), transparent 25%),
    linear-gradient(110deg, #292725 0%, #47402d 68%, #bd9200 150%);
}

.hero-copy {
  max-width: 550px;
}

.hero-kicker,
.section-kicker {
  color: rgb(var(--v-theme-primary));
  font-size: 0.72rem;
  font-weight: 850;
  letter-spacing: 0.12em;
}

.hero-copy h1 {
  margin: 12px 0 8px;
  font-size: clamp(2rem, 4vw, 3.2rem);
  font-weight: 900;
  letter-spacing: -0.045em;
  line-height: 1.04;
}

.hero-copy p {
  margin-bottom: 20px;
  color: rgb(255 255 255 / 82%);
}

.hero-tool {
  margin-right: 5%;
  color: rgb(var(--v-theme-primary));
  filter: drop-shadow(0 10px 24px rgb(0 0 0 / 25%));
  transform: rotate(-12deg);
}

.hero-deals {
  display: grid;
  grid-template-rows: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.side-offer {
  display: grid;
  min-height: 150px;
  grid-template-columns: minmax(80px, 0.9fr) minmax(100px, 1.1fr);
  align-items: center;
  gap: 10px;
  padding: 14px;
  border: 1px solid #e8e5de;
  border-radius: 7px;
  color: #292725;
  background: #fff;
  cursor: pointer;
  text-align: left;
  transition: box-shadow 160ms ease, transform 160ms ease;
}

.side-offer:hover {
  box-shadow: 0 8px 20px rgb(34 32 33 / 9%);
  transform: translateY(-2px);
}

.side-offer-image {
  position: relative;
  display: grid;
  height: 108px;
  place-items: center;
  color: #9c8a47;
  background: #f5f3ed;
}

.side-offer-image :deep(.v-img) {
  width: 100%;
  height: 100%;
}

.side-discount {
  position: absolute;
  top: 5px;
  left: 5px;
  padding: 3px 6px;
  color: #262321;
  background: rgb(var(--v-theme-primary));
  font-size: 0.67rem;
  font-weight: 900;
}

.side-offer-copy {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.side-offer-copy span {
  color: #9a7800;
  font-size: 0.61rem;
  font-weight: 850;
  letter-spacing: 0.08em;
}

.side-offer-copy strong {
  display: -webkit-box;
  overflow: hidden;
  color: #292725;
  font-size: 0.84rem;
  line-height: 1.3;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.side-offer-copy b {
  color: #a53a21;
  font-size: 0.9rem;
}

.side-offer-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 1px solid #e8e5de;
  border-radius: 7px;
  color: #898378;
  background: #fff;
  font-size: 0.8rem;
}

.category-section,
.sale-section,
.featured-section,
.category-showcase,
.brand-strip {
  margin-bottom: 30px;
}

.company-hub {
  margin-bottom: 34px;
  padding: clamp(20px, 3vw, 32px);
  border: 1px solid #e8e5de;
  border-radius: 12px;
  background: #fff;
}

.company-hub-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 20px;
}

.company-hub-heading h2 {
  margin-top: 5px;
  color: #292725;
  font-size: clamp(1.35rem, 3vw, 1.8rem);
  font-weight: 900;
  letter-spacing: -0.04em;
}

.company-hub-heading p {
  margin: 5px 0 0;
  color: #777168;
  font-size: 0.9rem;
}

.company-hub-icon {
  color: #a39d92;
}

.company-card-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.company-card {
  overflow: hidden;
  padding: 0;
  border: 1px solid #e8e5de;
  border-radius: 10px;
  color: #292725;
  background: #fff;
  cursor: pointer;
  text-align: left;
  transition: box-shadow 180ms ease, transform 180ms ease;
}

.company-card:hover {
  box-shadow: 0 14px 32px rgb(34 32 33 / 12%);
  transform: translateY(-3px);
}

.company-card-image {
  display: flex;
  min-height: 190px;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-end;
  gap: 5px;
  padding: 22px;
  color: #fff;
  background-position: center;
  background-size: cover;
}

.company-logo-mark {
  display: grid;
  width: 38px;
  height: 38px;
  margin-bottom: 4px;
  place-items: center;
  border: 1px solid rgb(255 255 255 / 62%);
  border-radius: 50%;
  color: var(--company-color);
  background: #fff;
  font-size: 1.15rem;
  font-weight: 950;
}

.company-card-name {
  font-size: 1.65rem;
  font-weight: 950;
  letter-spacing: -0.04em;
  text-transform: uppercase;
}

.company-card-tagline {
  max-width: 290px;
  color: rgb(255 255 255 / 86%);
  font-size: 0.83rem;
  font-weight: 550;
}

.company-card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 52px;
  padding: 0 18px;
  border-top: 3px solid var(--company-color);
  color: #555149;
  font-size: 0.8rem;
  font-weight: 750;
}

.company-card-footer .v-icon {
  color: var(--company-color);
}

.category-section {
  padding: 16px 18px 18px;
  border: 1px solid #e8e5de;
  border-radius: 7px;
  background: #fff;
}

.category-heading,
.section-title,
.featured-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 14px;
}

.category-heading h2 {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #292725;
  font-size: 1rem;
  font-weight: 850;
}

.category-heading a {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  color: #777168;
  font-size: 0.78rem;
  font-weight: 700;
  text-decoration: none;
}

.category-strip {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 10px;
}

.category-tile {
  display: flex;
  min-height: 96px;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px;
  border: 1px solid #e8e5de;
  color: #38342f;
  background: #fff;
  cursor: pointer;
  text-align: center;
  transition: border-color 150ms ease, background 150ms ease;
}

.category-tile:hover {
  border-color: rgb(var(--v-theme-primary));
  background: #fffdf5;
}

.category-icon {
  display: grid;
  width: 38px;
  height: 38px;
  place-items: center;
  border-radius: 50%;
  color: #292725;
  background: rgb(var(--v-theme-primary) / 28%);
}

.category-tile > span:last-child {
  display: -webkit-box;
  overflow: hidden;
  min-height: 2.3em;
  font-size: 0.7rem;
  font-weight: 750;
  line-height: 1.2;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.section-title h2,
.featured-heading h2 {
  margin-top: 3px;
  color: #292725;
  font-size: 1.4rem;
  font-weight: 900;
  letter-spacing: -0.035em;
}

.sale-layout {
  display: grid;
  grid-template-columns: minmax(170px, 0.9fr) minmax(0, 3.1fr);
  gap: 12px;
}

.sale-promo {
  position: relative;
  display: flex;
  min-height: 310px;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-end;
  overflow: hidden;
  padding: 20px;
  color: #fff;
  background:
    linear-gradient(0deg, rgb(20 19 18 / 93%) 0%, rgb(20 19 18 / 32%) 80%),
    linear-gradient(135deg, #44403a, #1d1b19);
}

.sale-stamp {
  position: absolute;
  top: 18px;
  left: 17px;
  padding: 5px 9px;
  color: #242222;
  background: rgb(var(--v-theme-primary));
  font-size: 0.65rem;
  font-weight: 900;
  line-height: 1.15;
}

.sale-tool {
  position: absolute;
  top: 50%;
  left: 50%;
  color: rgb(var(--v-theme-primary) / 85%);
  transform: translate(-50%, -68%) rotate(-12deg);
}

.sale-copy {
  position: relative;
  font-size: 0.8rem;
  font-weight: 700;
}

.sale-promo strong {
  position: relative;
  margin-top: 3px;
  color: rgb(var(--v-theme-primary));
  font-size: clamp(1.7rem, 3vw, 2.5rem);
  font-weight: 950;
  letter-spacing: -0.04em;
}

.sale-products,
.featured-products,
.showcase-products {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}

.featured-section {
  padding: 18px;
  border-radius: 7px;
  background: #fff;
}

.featured-products {
  grid-template-columns: repeat(5, minmax(0, 1fr));
}

.brand-strip {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding: 20px 24px;
  border: 1px solid #e8e5de;
  background: #fff;
}

.brand-label {
  color: #867100;
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.1em;
}

.brand-list {
  display: flex;
  flex: 1;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-evenly;
  gap: 12px 22px;
}

.brand-list span {
  color: #38342f;
  font-size: 1rem;
  font-weight: 900;
  text-transform: uppercase;
}

.showcase-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  min-height: 104px;
  padding: 18px 24px;
  color: #fff;
  background:
    linear-gradient(90deg, rgb(24 23 22 / 94%), rgb(24 23 22 / 64%)),
    linear-gradient(110deg, #252321, #665316);
}

.showcase-banner-alt {
  background:
    linear-gradient(90deg, rgb(24 23 22 / 94%), rgb(24 23 22 / 64%)),
    linear-gradient(110deg, #252321, #394a3d);
}

.showcase-banner h2 {
  margin-top: 4px;
  font-size: clamp(1.2rem, 3vw, 1.7rem);
  font-weight: 850;
}

.showcase-products {
  grid-template-columns: repeat(4, minmax(0, 1fr));
  padding: 12px;
  background: #fff;
}

.empty-state {
  padding: 28px;
  border: 1px dashed #d7d2c8;
  color: #777168;
  background: #fff;
  text-align: center;
}

@media (max-width: 960px) {
  .hero-grid {
    grid-template-columns: minmax(0, 1.8fr) minmax(205px, 1fr);
  }

  .featured-products {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .brand-strip {
    align-items: flex-start;
    flex-direction: column;
  }

  .brand-list {
    justify-content: flex-start;
    width: 100%;
  }
}

@media (max-width: 650px) {
  .hero-grid {
    grid-template-columns: 1fr;
  }

  .hero-slide {
    min-height: 275px;
    padding: 24px;
  }

  .hero-tool {
    margin: 0;
    font-size: 86px !important;
  }

  .hero-deals {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    grid-template-rows: auto;
  }

  .side-offer {
    grid-template-columns: 1fr;
    padding: 10px;
  }

  .side-offer-image {
    height: 100px;
  }

  .side-offer-copy strong {
    min-height: 2.6em;
  }

  .category-strip {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .sale-layout {
    grid-template-columns: 1fr;
  }

  .sale-promo {
    min-height: 190px;
  }

  .sale-products,
  .featured-products,
  .showcase-products {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .section-title,
  .featured-heading {
    align-items: flex-start;
  }

  .brand-strip {
    padding: 16px;
  }

  .company-card-grid {
    grid-template-columns: 1fr;
  }
}
</style>
