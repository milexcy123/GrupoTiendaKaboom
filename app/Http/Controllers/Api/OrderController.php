<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    // GET /api/orders (historial del cliente)
    public function index(Request $request)
    {
        $orders = Order::with(['items.product:id,name,image', 'address'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return response()->json($orders, 200);
    }

    // GET /api/orders/{id} (solo si el pedido es del cliente)
    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product:id,name,image', 'address'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (! $order) {
            return response()->json(['message' => 'Pedido no encontrado.'], 404);
        }

        return response()->json($order, 200);
    }

    // POST /api/orders (crea el pedido a partir del carrito)
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'delivery_type'  => 'required|in:home,pickup',
            'payment_method' => 'required|in:cash_on_delivery,pay_at_store',
            'address_id'     => 'required_if:delivery_type,home|nullable|exists:addresses,id',
        ]);

        // Regla: a domicilio se paga contra entrega; retiro en tienda se paga en tienda
        if ($data['delivery_type'] === 'home' && $data['payment_method'] !== 'cash_on_delivery') {
            return response()->json([
                'message' => 'La entrega a domicilio se paga contra entrega.',
            ], 422);
        }

        if ($data['delivery_type'] === 'pickup' && $data['payment_method'] !== 'pay_at_store') {
            return response()->json([
                'message' => 'El retiro en tienda se paga en tienda.',
            ], 422);
        }

        // La dirección debe ser del mismo cliente
        $addressId = null;

        if ($data['delivery_type'] === 'home') {
            $address = Address::where('id', $data['address_id'])
                ->where('user_id', $user->id)
                ->first();

            if (! $address) {
                return response()->json(['message' => 'La dirección no es válida.'], 422);
            }

            $addressId = $address->id;
        }

        // Todo o nada: si algo falla, no se crea el pedido ni se descuenta inventario
        $order = DB::transaction(function () use ($user, $data, $addressId) {
            $cart = Cart::with('items')->where('user_id', $user->id)->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Tu carrito está vacío.',
                ]);
            }

            $lineas = [];
            $total = 0;

            foreach ($cart->items as $item) {
                // Bloquea la fila para que dos compras a la vez no descuenten mal el inventario
                $product = Product::lockForUpdate()->find($item->product_id);

                if (! $product || $product->status !== 'active' || $product->stock < $item->quantity) {
                    $nombre = $product ? $product->name : 'un producto';

                    throw ValidationException::withMessages([
                        'cart' => "No hay existencias suficientes de {$nombre}.",
                    ]);
                }

                $precio = $product->finalPrice();
                $subtotal = round($precio * $item->quantity, 2);
                $total += $subtotal;

                $lineas[] = [
                    'product'    => $product,
                    'quantity'   => $item->quantity,
                    'unit_price' => $precio,
                    'subtotal'   => $subtotal,
                ];
            }

            $order = Order::create([
                'user_id'        => $user->id,
                'address_id'     => $addressId,
                'delivery_type'  => $data['delivery_type'],
                'payment_method' => $data['payment_method'],
                'total'          => round($total, 2),
                'status'         => 'received',
                'payment_status' => 'pending',
            ]);

            foreach ($lineas as $linea) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $linea['product']->id,
                    'quantity'   => $linea['quantity'],
                    'unit_price' => $linea['unit_price'],
                    'subtotal'   => $linea['subtotal'],
                ]);

                // Descuenta inventario; si llega a 0, el producto queda agotado
                $producto = $linea['product'];
                $producto->stock = $producto->stock - $linea['quantity'];
                $producto->status = $producto->stock > 0 ? 'active' : 'sold_out';
                $producto->save();
            }

            // Vacía el carrito
            $cart->items()->delete();

            return $order;
        });

        return response()->json(
            $order->load(['items.product:id,name,image', 'address']),
            201
        );
    }
}