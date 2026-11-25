<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return response()->json($categories, 200);
    }

    // GET /api/categories-with-products
    public function withProducts()
    {
        $categories = Category::with('products')->orderBy('name')->get();

        return response()->json($categories, 200);
    }

    // GET /api/categories/products-count (administrador)
    public function productsCount()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return response()->json($categories, 200);
    }

    // POST /api/categories (administrador)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    // PUT /api/categories/{id} (administrador)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        return response()->json($category, 200);
    }

    // DELETE /api/categories/{id} (administrador)
    public function destroy($id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        }

        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una categoría que tiene productos.',
            ], 409);
        }

        $category->delete();

        return response()->json(['message' => 'Categoría eliminada.'], 200);
    }
}