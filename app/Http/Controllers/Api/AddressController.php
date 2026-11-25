<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // GET /api/addresses (direcciones del cliente)
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderByDesc('id')->get();

        return response()->json($addresses, 200);
    }

    // POST /api/addresses
    public function store(Request $request)
    {
        $data = $request->validate([
            'address'   => 'required|string|max:255',
            'city'      => 'required|string|max:100',
            'reference' => 'nullable|string|max:255',
        ]);

        $address = $request->user()->addresses()->create($data);

        return response()->json($address, 201);
    }
}