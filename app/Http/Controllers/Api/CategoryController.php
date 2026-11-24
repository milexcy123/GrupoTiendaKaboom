<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

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
}