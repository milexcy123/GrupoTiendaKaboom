<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET /api/admin/dashboard (opcional: ?threshold=5 para "pocas existencias")
    public function summary(Request $request)
    {
        $umbral = (int) $request->query('threshold', 5);
        $inicioHoy = now()->startOfDay();
        $finHoy = now()->endOfDay();

        $pocasExistencias = Product::where('status', 'active')
            ->where('stock', '<=', $umbral)
            ->orderBy('stock')
            ->get(['id', 'name', 'stock']);

        return response()->json([
            'new_orders'      => Order::where('status', 'received')->count(),
            'orders_today'    => Order::whereBetween('created_at', [$inicioHoy, $finHoy])->count(),
            'sales_today'     => round((float) Order::where('status', '!=', 'canceled')
                                    ->whereBetween('created_at', [$inicioHoy, $finHoy])
                                    ->sum('total'), 2),
            'pending_payment' => Order::where('status', '!=', 'canceled')
                                    ->where('payment_status', 'pending')
                                    ->count(),
            'customers'       => User::where('role', 'customer')->count(),
            'sold_out'        => Product::where('status', 'sold_out')->count(),
            'low_stock_count' => $pocasExistencias->count(),
            'low_stock'       => $pocasExistencias,
        ], 200);
    }
}