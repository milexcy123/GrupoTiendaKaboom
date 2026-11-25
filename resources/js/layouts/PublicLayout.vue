<template>
  <div class="store-announcement">
    <v-container class="d-flex justify-space-between align-center py-0">
      <span>Herramientas y soluciones para tus proyectos</span>
      <span class="d-none d-sm-inline">Grupo Kaboom Express</span>
    </v-container>
  </div>

  <v-app-bar color="surface" elevation="0" height="84" class="store-header">
    <v-container class="header-content d-flex align-center py-0">
      <v-app-bar-nav-icon v-if="!mdAndUp" aria-label="Abrir menú" @click="drawer = !drawer" />

      <router-link to="/" class="brand-link">
        <span class="brand-symbol"><v-icon size="25">mdi-tools</v-icon></span>
        <span class="brand-copy">
          <strong>Kaboom<span>Express</span></strong>
          <small>Herramientas para avanzar</small>
        </span>
      </router-link>

      <div v-if="mdAndUp && companyContacts.length" class="header-contacts">
        <div v-for="item in companyContacts" :key="item.id" class="header-contact">
          <v-icon size="20">{{ item.phone ? 'mdi-phone-outline' : 'mdi-email-outline' }}</v-icon>
          <span>
            <small>{{ item.name }}</small>
            <strong>{{ item.phone || item.email }}</strong>
          </span>
        </div>
      </div>

      <v-spacer />

      <v-btn icon to="/carrito" aria-label="Abrir carrito" class="cart-button">
        <v-badge :content="cart.count" :model-value="cart.count > 0" color="primary">
          <v-icon>mdi-cart-outline</v-icon>
        </v-badge>
      </v-btn>
      <span v-if="mdAndUp" class="cart-label">Mi carrito</span>

      <template v-if="auth.user">
        <v-menu>
          <template #activator="{ props }">
            <v-btn v-bind="props" prepend-icon="mdi-account-circle-outline" variant="text" class="account-button">
              <span v-if="mdAndUp">{{ auth.user.name.split(' ')[0] }}</span>
            </v-btn>
          </template>
          <v-list>
            <v-list-item to="/pedidos" prepend-icon="mdi-package-variant-closed" title="Mis pedidos" />
            <v-list-item v-if="isAdmin" to="/admin" prepend-icon="mdi-view-dashboard" title="Administración" />
            <v-list-item prepend-icon="mdi-logout" title="Cerrar sesión" @click="salir" />
          </v-list>
        </v-menu>
      </template>
      <template v-else>
        <v-btn v-if="mdAndUp" to="/login" variant="text" class="account-button">Ingresar</v-btn>
        <v-btn v-if="mdAndUp" to="/registro" color="primary" variant="flat" class="register-button">Crear cuenta</v-btn>
        <v-btn v-else icon to="/login" aria-label="Ingresar" class="account-button">
          <v-icon>mdi-account-circle-outline</v-icon>
        </v-btn>
      </template>
    </v-container>
  </v-app-bar>

  <div v-if="!mdAndUp" class="mobile-search">
    <v-container class="py-2">
      <v-text-field
        v-model="search"
        placeholder="¿Qué estás buscando?"
        prepend-inner-icon="mdi-magnify"
        variant="solo"
        density="compact"
        hide-details
        @keyup.enter="buscar"
      />
    </v-container>
  </div>

  <v-app-bar v-if="mdAndUp" color="secondary" elevation="0" height="48" class="store-navigation">
    <v-container class="nav-content d-flex align-center py-0">
      <v-btn to="/catalogo" color="primary" variant="flat" prepend-icon="mdi-view-grid" class="category-button">
        Categorías
      </v-btn>
      <v-btn
        v-for="link in links"
        :key="link.to"
        :to="link.to"
        variant="text"
        class="nav-link"
        :class="{ 'nav-link-active': $route.path === link.to }"
      >
        {{ link.title }}
      </v-btn>
      <v-spacer />
      <v-menu>
        <template #activator="{ props }">
          <v-btn v-bind="props" variant="text" append-icon="mdi-chevron-down" class="company-selector">
            {{ selectedCompanyName }}
          </v-btn>
        </template>
        <v-list>
          <v-list-item
            v-for="option in companyOptions"
            :key="option.value"
            :title="option.name"
            :active="company.selected === option.value"
            @click="elegir(option.value)"
          />
        </v-list>
      </v-menu>
      <v-text-field
        v-model="search"
        placeholder="Buscar"
        prepend-inner-icon="mdi-magnify"
        variant="solo"
        density="compact"
        hide-details
        single-line
        class="nav-search ml-4"
        aria-label="Buscar productos"
        @keyup.enter="buscar"
      />
    </v-container>
  </v-app-bar>

  <v-navigation-drawer v-model="drawer" temporary>
    <v-list>
      <v-list-item
        v-for="link in links"
        :key="link.to"
        :to="link.to"
        :title="link.title"
        @click="drawer = false"
      />
      <v-divider class="my-2" />
      <v-list-subheader>Empresa</v-list-subheader>
      <v-list-item
        v-for="option in companyOptions"
        :key="option.value"
        :title="option.name"
        :active="company.selected === option.value"
        @click="elegir(option.value)"
      />
    </v-list>
  </v-navigation-drawer>

  <v-main>
    <router-view />
  </v-main>

  <v-footer color="secondary" class="store-footer flex-column">
    <v-container>
      <div class="footer-main">
        <div class="footer-brand">
          <div class="brand-link footer-logo">
            <span class="brand-symbol"><v-icon size="25">mdi-tools</v-icon></span>
            <span class="brand-copy">
              <strong>Kaboom<span>Express</span></strong>
              <small>Herramientas para avanzar</small>
            </span>
          </div>
          <p>Soluciones y herramientas para acompañar cada uno de tus proyectos.</p>
        </div>
        <div v-for="c in company.list" :key="c.id" class="footer-company">
          <h3 :style="{ borderColor: c.primary_color }">{{ c.name }}</h3>
          <a v-if="c.phone" :href="`tel:${c.phone}`"><v-icon size="16">mdi-phone-outline</v-icon>{{ c.phone }}</a>
          <a v-if="c.email" :href="`mailto:${c.email}`"><v-icon size="16">mdi-email-outline</v-icon>{{ c.email }}</a>
          <span v-if="!c.phone && !c.email">Información de contacto próximamente</span>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© {{ new Date().getFullYear() }} Grupo Kaboom Express</span>
        <span>Calidad y servicio para cada proyecto</span>
      </div>
    </v-container>
  </v-footer>

  <v-snackbar v-model="toast.show" :color="toast.color" timeout="3000">{{ toast.text }}</v-snackbar>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useDisplay } from 'vuetify';
import { auth, isAdmin, logout } from '../stores/auth';
import { cart, loadCart, clearCartState } from '../stores/cart';
import { company, selectCompany } from '../stores/company';
import { toast } from '../stores/toast';

const router = useRouter();
const { mdAndUp } = useDisplay();
const drawer = ref(false);
const search = ref('');

const links = [
  { title: 'Inicio', to: '/' },
  { title: 'Catálogo', to: '/catalogo' },
  { title: 'Ofertas', to: '/ofertas' },
  { title: 'Contacto', to: '/contacto' },
  { title: 'Quiénes somos', to: '/quienes-somos' },
];

const companyOptions = computed(() => [
  { value: 'all', name: 'Todas' },
  ...company.list.map((c) => ({ value: c.slug, name: c.name })),
]);
const selectedCompanyName = computed(
  () => companyOptions.value.find((option) => option.value === company.selected)?.name || 'Todas'
);
const companyContacts = computed(() => company.list.filter((item) => item.phone || item.email).slice(0, 2));

// El carrito se recarga cada vez que el usuario inicia o cierra sesión
watch(
  () => auth.user,
  () => loadCart().catch(() => clearCartState()),
  { immediate: true }
);

function buscar() {
  router.push({ name: 'catalog', query: { search: search.value.trim() || undefined } });
}

function elegir(slug) {
  selectCompany(slug);
  drawer.value = false;
  router.push(slug === 'all' ? { name: 'home' } : { name: 'company-home', params: { slug } });
}

async function salir() {
  await logout();
  clearCartState();
  router.push('/');
}
</script>

<style scoped>
.store-announcement {
  min-height: 28px;
  color: #242222;
  background: rgb(var(--v-theme-primary));
  font-size: 0.75rem;
  font-weight: 650;
}

.store-header {
  border-bottom: 1px solid #eeece7;
}

.header-content,
.nav-content {
  max-width: 1280px;
}

.brand-link {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  flex: 0 0 auto;
  color: #242222;
  text-decoration: none;
}

.brand-symbol {
  display: grid;
  width: 46px;
  height: 46px;
  place-items: center;
  border-radius: 50%;
  color: #242222;
  background: rgb(var(--v-theme-primary));
}

.brand-copy {
  display: flex;
  flex-direction: column;
  line-height: 1;
}

.brand-copy strong {
  font-size: 1.15rem;
  font-weight: 900;
  letter-spacing: -0.045em;
}

.brand-copy strong span {
  color: #8a6c00;
}

.brand-copy small {
  margin-top: 5px;
  color: #777168;
  font-size: 0.66rem;
  font-weight: 600;
}

.header-search {
  max-width: 520px;
  border: 1px solid #e8e5de;
  border-radius: 9px;
  background: #f7f6f3;
}

.header-contacts {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-left: clamp(20px, 4vw, 58px);
}

.header-contact {
  display: flex;
  align-items: center;
  gap: 8px;
  max-width: 190px;
  color: #777168;
}

.header-contact span {
  display: flex;
  min-width: 0;
  flex-direction: column;
}

.header-contact small {
  color: #8a857a;
  font-size: 0.63rem;
}

.header-contact strong {
  overflow: hidden;
  color: #38342f;
  font-size: 0.72rem;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.cart-button,
.account-button {
  color: #292725;
}

.cart-label {
  margin-right: 20px;
  color: #4a4640;
  font-size: 0.8rem;
  font-weight: 700;
}

.register-button {
  color: #242222;
}

.store-navigation :deep(.v-toolbar__content) {
  height: 48px !important;
}

.nav-link,
.company-selector {
  height: 48px;
  border-radius: 0;
  color: rgb(255 255 255 / 84%);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}

.nav-link-active,
.nav-link:hover {
  color: rgb(var(--v-theme-primary));
}

.category-button {
  height: 38px;
  margin-right: 10px;
  color: #242222;
  font-size: 0.74rem;
  font-weight: 850;
  text-transform: uppercase;
}

.company-selector {
  flex: 0 0 auto;
  color: rgb(var(--v-theme-primary));
}

.nav-search {
  max-width: 190px;
  min-width: 140px;
}

.nav-search :deep(.v-field) {
  min-height: 34px;
  border-radius: 4px;
}

.mobile-search {
  border-bottom: 1px solid #eeece7;
  background: #fff;
}

.store-footer {
  padding: 38px 0 0;
  color: #fff;
}

.footer-main {
  display: grid;
  grid-template-columns: minmax(240px, 1.5fr) repeat(2, minmax(180px, 1fr));
  gap: 36px;
  padding-bottom: 30px;
}

.footer-logo {
  color: #fff;
}

.footer-logo .brand-copy small {
  color: rgb(255 255 255 / 62%);
}

.footer-brand p {
  max-width: 320px;
  margin-top: 16px;
  color: rgb(255 255 255 / 66%);
  font-size: 0.88rem;
  line-height: 1.6;
}

.footer-company h3 {
  margin: 3px 0 16px;
  padding-left: 12px;
  border-left: 3px solid;
  font-size: 1rem;
  font-weight: 800;
}

.footer-company a,
.footer-company span {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 10px 0;
  color: rgb(255 255 255 / 68%);
  font-size: 0.82rem;
  text-decoration: none;
}

.footer-company a:hover {
  color: rgb(var(--v-theme-primary));
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  padding: 18px 0;
  border-top: 1px solid rgb(255 255 255 / 14%);
  color: rgb(255 255 255 / 56%);
  font-size: 0.75rem;
}

@media (max-width: 960px) {
  .footer-main {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 26px;
  }

  .footer-brand {
    grid-column: 1 / -1;
  }
}

@media (max-width: 600px) {
  .brand-symbol {
    width: 40px;
    height: 40px;
  }

  .brand-copy strong {
    font-size: 1.02rem;
  }

  .store-header :deep(.v-toolbar__content) {
    padding-inline: 8px;
  }

  .footer-main {
    grid-template-columns: 1fr;
  }

  .footer-brand {
    grid-column: auto;
  }

  .footer-bottom {
    flex-direction: column;
  }
}
</style>