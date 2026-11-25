<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    // Lee el período (por defecto, los últimos 30 días)
    private function range(Request $request): array
    {
        $request->validate([
            'from'  => 'nullable|date',
            'to'    => 'nullable|date',
            'group' => 'nullable|in:day,month',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        if ($to->lt($from)) {
            throw ValidationException::withMessages([
                'to' => 'La fecha final debe ser igual o posterior a la inicial.',
            ]);
        }

        return [$from, $to];
    }

    // GET /api/admin/reports/sales?from=2026-11-01&to=2026-11-30&group=day
    public function sales(Request $request)
    {
        [$from, $to] = $this->range($request);

        $group = $request->query('group', 'day');
        $formato = $group === 'month' ? '%Y-%m' : '%Y-%m-%d';

        $rows = Order::where('status', '!=', 'canceled')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '{$formato}') as period, COUNT(*) as orders, SUM(total) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn ($r) => [
                'period' => $r->period,
                'orders' => (int) $r->orders,
                'total'  => round((float) $r->total, 2),
            ]);

        return response()->json([
            'from'   => $from->toDateString(),
            'to'     => $to->toDateString(),
            'group'  => $group,
            'totals' => [
                'orders' => $rows->sum('orders'),
                'total'  => round($rows->sum('total'), 2),
            ],
            'rows'   => $rows->values(),
        ], 200);
    }

    // GET /api/admin/reports/top-products?from=...&to=...&limit=10
    public function topProducts(Request $request)
    {
        [$from, $to] = $this->range($request);
        $limit = (int) $request->query('limit', 10);

        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', '!=', 'canceled')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as units_sold, SUM(order_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'name'       => $r->name,
                'units_sold' => (int) $r->units_sold,
                'revenue'    => round((float) $r->revenue, 2),
            ]);

        return response()->json([
            'from' => $from->toDateString(),
            'to'   => $to->toDateString(),
            'rows' => $rows->values(),
        ], 200);
    }

    // GET /api/admin/reports/payments?from=...&to=... (pagados vs pendientes de pago)
    public function payments(Request $request)
    {
        [$from, $to] = $this->range($request);

        $rows = Order::where('status', '!=', 'canceled')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('payment_status, COUNT(*) as orders, SUM(total) as total')
            ->groupBy('payment_status')
            ->get();

        $resultado = [
            'paid'    => ['orders' => 0, 'total' => 0],
            'pending' => ['orders' => 0, 'total' => 0],
        ];

        foreach ($rows as $r) {
            $resultado[$r->payment_status] = [
                'orders' => (int) $r->orders,
                'total'  => round((float) $r->total, 2),
            ];
        }

        return response()->json([
            'from'    => $from->toDateString(),
            'to'      => $to->toDateString(),
            'paid'    => $resultado['paid'],
            'pending' => $resultado['pending'],
        ], 200);
    }
}