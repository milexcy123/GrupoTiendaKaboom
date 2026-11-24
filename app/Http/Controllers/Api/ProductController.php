<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Consulta base: trae categoría, empresa y ofertas vigentes de cada producto
    private function baseQuery()
    {
        return Product::with([
            'category:id,name',
            'company:id,name,slug',
            'offers' => function ($query) {
                $query->whereDate('start_date', '<=', today())
                      ->whereDate('end_date', '>=', today());
            },
        ]);
    }

    // GET /api/products (búsqueda y filtros por parámetros)
    public function index(Request $request)
    {
        $products = $this->baseQuery()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->company_id, fn ($q, $v) => $q->where('company_id', $v))
            ->when($request->brand, fn ($q, $v) => $q->where('brand', $v))
            ->when($request->min_price, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($request->max_price, fn ($q, $v) => $q->where('price', '<=', $v))
            ->orderBy('name')
            ->get();

        return response()->json($products, 200);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = $this->baseQuery()->find($id);

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        return response()->json($product, 200);
    }

    // GET /api/products/by-category/{id}
    public function byCategory($id)
    {
        $products = $this->baseQuery()
            ->where('category_id', $id)
            ->orderBy('name')
            ->get();

        return response()->json($products, 200);
    }

    // POST /api/products (administrador)
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'brand'       => 'nullable|string|max:255',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|string|max:255',
        ]);

        // Regla de negocio: sin existencias el producto queda agotado
        $data['status'] = $data['stock'] > 0 ? 'active' : 'sold_out';

        $product = Product::create($data);

        return response()->json(
            $product->load(['category:id,name', 'company:id,name,slug']),
            201
        );
    }

    // PUT /api/products/{id} (administrador)
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        $data = $request->validate([
            'company_id'  => 'sometimes|required|exists:companies,id',
            'category_id' => 'sometimes|required|exists:categories,id',
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'brand'       => 'nullable|string|max:255',
            'stock'       => 'sometimes|required|integer|min:0',
            'image'       => 'nullable|string|max:255',
        ]);

        if (array_key_exists('stock', $data)) {
            $data['status'] = $data['stock'] > 0 ? 'active' : 'sold_out';
        }

        $product->update($data);

        return response()->json(
            $product->load(['category:id,name', 'company:id,name,slug']),
            200
        );
    }

    // DELETE /api/products/{id} (administrador)
    public function destroy($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        try {
            $product->delete();
        } catch (QueryException $e) {
            // El producto ya aparece en pedidos: no se puede borrar sin perder el historial
            return response()->json([
                'message' => 'No se puede eliminar un producto que ya tiene pedidos. Déjalo en 0 existencias.',
            ], 409);
        }

        return response()->json(['message' => 'Producto eliminado.'], 200);
    }
}