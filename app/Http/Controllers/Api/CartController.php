<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Obtiene (o crea) el carrito del cliente autenticado
    private function currentCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    // Respuesta estándar: productos, precio con oferta, subtotales y total
    private function cartResponse(Cart $cart, int $status = 200)
    {
        $cart->load('items.product.company:id,name,slug');

        $items = $cart->items->map(function ($item) {
            $precio = $item->product->finalPrice();

            return [
                'id'         => $item->id,
                'quantity'   => $item->quantity,
                'unit_price' => $precio,
                'subtotal'   => round($precio * $item->quantity, 2),
                'product'    => $item->product,
            ];
        })->values();

        return response()->json([
            'id'    => $cart->id,
            'items' => $items,
            'total' => round($items->sum('subtotal'), 2),
        ], $status);
    }

    // GET /api/cart
    public function show(Request $request)
    {
        return $this->cartResponse($this->currentCart($request));
    }

    // POST /api/cart/items
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::find($data['product_id']);

        if ($product->status !== 'active' || $product->stock < 1) {
            return response()->json(['message' => 'Este producto está agotado.'], 422);
        }

        $cart = $this->currentCart($request);
        $item = $cart->items()->where('product_id', $product->id)->first();
        $nuevaCantidad = ($item ? $item->quantity : 0) + $data['quantity'];

        if ($nuevaCantidad > $product->stock) {
            return response()->json([
                'message' => "Solo hay {$product->stock} unidades disponibles.",
            ], 422);
        }

        if ($item) {
            $item->update(['quantity' => $nuevaCantidad]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $data['quantity'],
            ]);
        }

        return $this->cartResponse($cart, 201);
    }

    // PUT /api/cart/items/{id}
    public function updateItem(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->currentCart($request);
        $item = $cart->items()->with('product')->find($id);

        if (! $item) {
            return response()->json(['message' => 'Producto no encontrado en tu carrito.'], 404);
        }

        if ($data['quantity'] > $item->product->stock) {
            return response()->json([
                'message' => "Solo hay {$item->product->stock} unidades disponibles.",
            ], 422);
        }

        $item->update(['quantity' => $data['quantity']]);

        return $this->cartResponse($cart);
    }

    // DELETE /api/cart/items/{id}
    public function removeItem(Request $request, $id)
    {
        $cart = $this->currentCart($request);
        $item = $cart->items()->find($id);

        if (! $item) {
            return response()->json(['message' => 'Producto no encontrado en tu carrito.'], 404);
        }

        $item->delete();

        return $this->cartResponse($cart);
    }
}