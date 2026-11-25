<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /api/admin/customers (opcional: ?search=nombre o correo)
    public function index(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->select(['id', 'name', 'email', 'phone', 'created_at'])
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', '!=', 'canceled');
            }], 'total')
            ->orderBy('name')
            ->get();

        $customers->each(function ($cliente) {
            $cliente->total_spent = round((float) $cliente->total_spent, 2);
        });

        return response()->json($customers, 200);
    }
}