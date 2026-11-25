import { createRouter, createWebHistory } from 'vue-router';
import PublicLayout from '../layouts/PublicLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import { auth, loadUser } from '../stores/auth';

// Carga cada pantalla desde resources/js/pages; si el archivo no existe, usa Pending.vue
const modules = import.meta.glob('../pages/**/*.vue');
const page = (name) => modules[`../pages/${name}.vue`] || modules['../pages/Pending.vue'];

const routes = [
    {
        path: '/',
        component: PublicLayout,
        children: [
            { path: '', name: 'home', component: page('Home') },
            { path: 'empresa/:slug', name: 'company-home', component: page('CompanyHome') },
            { path: 'catalogo', name: 'catalog', component: page('Catalog') },
            { path: 'producto/:id', name: 'product', component: page('ProductDetail') },
            { path: 'ofertas', name: 'offers', component: page('Offers') },
            { path: 'contacto', name: 'contact', component: page('Contact') },
            { path: 'quienes-somos', name: 'about', component: page('About') },
            { path: 'login', name: 'login', component: page('Login'), meta: { guestOnly: true } },
            { path: 'registro', name: 'register', component: page('Register'), meta: { guestOnly: true } },
            { path: 'carrito', name: 'cart', component: page('Cart'), meta: { requiresAuth: true } },
            { path: 'checkout', name: 'checkout', component: page('Checkout'), meta: { requiresAuth: true } },
            { path: 'pedidos', name: 'orders', component: page('Orders'), meta: { requiresAuth: true } },
            { path: 'pedidos/:id', name: 'order-detail', component: page('OrderDetail'), meta: { requiresAuth: true } },
        ],
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAdmin: true },
        children: [
            { path: '', name: 'admin-dashboard', component: page('admin/Dashboard') },
            { path: 'pedidos', name: 'admin-orders', component: page('admin/Orders') },
            { path: 'pedidos/:id', name: 'admin-order-detail', component: page('admin/OrderDetail') },
            { path: 'productos', name: 'admin-products', component: page('admin/Products') },
            { path: 'categorias', name: 'admin-categories', component: page('admin/Categories') },
            { path: 'ofertas', name: 'admin-offers', component: page('admin/Offers') },
            { path: 'banners', name: 'admin-banners', component: page('admin/Banners') },
            { path: 'clientes', name: 'admin-customers', component: page('admin/Customers') },
            { path: 'reportes', name: 'admin-reports', component: page('admin/Reports') },
        ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior: () => ({ top: 0 }),
});

// Protección de rutas
router.beforeEach(async (to) => {
    if (!auth.ready) {
        await loadUser();
    }

    if (to.meta.requiresAdmin) {
        if (!auth.user) return { name: 'login', query: { redirect: to.fullPath } };
        if (auth.user.role !== 'admin') return { name: 'home' };
    }

    if (to.meta.requiresAuth && !auth.user) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && auth.user) {
        return auth.user.role === 'admin' ? { name: 'admin-dashboard' } : { name: 'home' };
    }
});

export default router;