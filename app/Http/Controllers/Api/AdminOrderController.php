<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    // Cambios de estado permitidos según el tipo de entrega
    private const TRANSITIONS = [
        'home' => [
            'received'   => ['preparing', 'canceled'],
            'preparing'  => ['on_the_way', 'canceled'],
            'on_the_way' => ['delivered', 'canceled'],
        ],
        'pickup' => [
            'received'         => ['preparing', 'canceled'],
            'preparing'        => ['ready_for_pickup', 'canceled'],
            'ready_for_pickup' => ['delivered', 'canceled'],
        ],
    ];

    private function relations(): array
    {
        return ['user:id,name,email,phone', 'address', 'items.product:id,name,image'];
    }

    // GET /api/admin/orders (filtros: status, payment_status, from, to)
    public function index(Request $request)
    {
        $request->validate([
            'status'         => 'nullable|in:received,preparing,ready_for_pickup,on_the_way,delivered,canceled',
            'payment_status' => 'nullable|in:pending,paid',
            'from'           => 'nullable|date',
            'to'             => 'nullable|date',
        ]);

        $orders = Order::with($this->relations())
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->payment_status, fn ($q, $v) => $q->where('payment_status', $v))
            ->when($request->from, fn ($q, $v) => $q->where('created_at', '>=', Carbon::parse($v)->startOfDay()))
            ->when($request->to, fn ($q, $v) => $q->where('created_at', '<=', Carbon::parse($v)->endOfDay()))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return response()->json($orders, 200);
    }

    // GET /api/admin/orders/{id}
    public function show($id)
    {
        $order = Order::with($this->relations())->find($id);

        if (! $order) {
            return response()->json(['message' => 'Pedido no encontrado.'], 404);
        }

        return response()->json($order, 200);
    }

    // PUT /api/admin/orders/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:received,preparing,ready_for_pickup,on_the_way,delivered,canceled',
        ]);

        $order = Order::with('items')->find($id);

        if (! $order) {
            return response()->json(['message' => 'Pedido no encontrado.'], 404);
        }

        $nuevo = $data['status'];
        $permitidos = self::TRANSITIONS[$order->delivery_type][$order->status] ?? [];

        if (! in_array($nuevo, $permitidos)) {
            return response()->json([
                'message' => "No se puede pasar el pedido de '{$order->status}' a '{$nuevo}'.",
                'allowed' => $permitidos,
            ], 422);
        }

        DB::transaction(function () use ($order, $nuevo) {
            // Si se cancela, el inventario regresa a la tienda
            if ($nuevo === 'canceled') {
                foreach ($order->items as $item) {
                    $producto = Product::lockForUpdate()->find($item->product_id);

                    if ($producto) {
                        $producto->stock = $producto->stock + $item->quantity;
                        $producto->status = 'active';
                        $producto->save();
                    }
                }
            }

            $order->update(['status' => $nuevo]);
        });

        return response()->json($order->fresh($this->relations()), 200);
    }

    // PUT /api/admin/orders/{id}/payment
    public function updatePayment(Request $request, $id)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:pending,paid',
        ]);

        $order = Order::find($id);

        if (! $order) {
            return response()->json(['message' => 'Pedido no encontrado.'], 404);
        }

        if ($order->status === 'canceled') {
            return response()->json([
                'message' => 'No se puede cambiar el pago de un pedido cancelado.',
            ], 422);
        }

        $order->update(['payment_status' => $data['payment_status']]);

        return response()->json($order->fresh($this->relations()), 200);
    }
}