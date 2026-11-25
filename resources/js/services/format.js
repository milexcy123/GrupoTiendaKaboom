const moneda = new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' });

export const formatMoney = (value) => moneda.format(Number(value || 0));

export const formatDate = (value) =>
    value ? new Date(value).toLocaleString('es-HN', { dateStyle: 'medium', timeStyle: 'short' }) : '';

export const orderStatus = {
    received: { text: 'Recibido', color: 'info' },
    preparing: { text: 'En preparación', color: 'warning' },
    ready_for_pickup: { text: 'Listo para retirar', color: 'success' },
    on_the_way: { text: 'En camino', color: 'primary' },
    delivered: { text: 'Entregado', color: 'success' },
    canceled: { text: 'Cancelado', color: 'error' },
};

export const paymentStatus = {
    pending: { text: 'Pendiente de pago', color: 'warning' },
    paid: { text: 'Pagado', color: 'success' },
};

export const deliveryType = { home: 'A domicilio', pickup: 'Retiro en tienda' };

export const paymentMethod = { cash_on_delivery: 'Pago contra entrega', pay_at_store: 'Pago en tienda' };

// Precio de un producto con su mejor oferta vigente
export function productPrice(product) {
    const price = Number(product.price);
    const offer = (product.offers || []).reduce(
        (best, o) => (!best || Number(o.discount) > Number(best.discount) ? o : best),
        null
    );
    const discount = offer ? Number(offer.discount) : 0;
    const final = offer ? Math.round(price * (1 - discount / 100) * 100) / 100 : price;

    return { price, final, discount };
}