<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class LatestProductController extends Controller
{
    // GET /api/products-latest (novedades: los 8 productos disponibles más recientes)
    public function __invoke()
    {
        $products = Product::with(['category:id,name', 'company:id,name,slug'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        return response()->json($products, 200);
    }
}