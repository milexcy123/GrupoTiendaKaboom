<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // POST /api/contact
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($data);

        return response()->json([
            'message' => 'Mensaje recibido. Pronto nos pondremos en contacto contigo.',
        ], 201);
    }
}