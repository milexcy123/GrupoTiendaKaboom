<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    // Agrega el precio ya descontado a la oferta
    private function addPrice($offer)
    {
        $precio = (float) $offer->product->price;
        $offer->final_price = round($precio * (1 - ((float) $offer->discount) / 100), 2);

        return $offer;
    }

    // GET /api/offers (solo ofertas vigentes)
    public function index()
    {
        $offers = Offer::with('product:id,name,price,image,stock,status')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->get()
            ->map(fn ($offer) => $this->addPrice($offer));

        return response()->json($offers, 200);
    }

    // GET /api/admin/offers (todas, vigentes o no)
    public function adminIndex()
    {
        $offers = Offer::with('product:id,name,price,image,stock,status')
            ->orderByDesc('id')
            ->get()
            ->map(function ($offer) {
                $inicio = Carbon::parse($offer->start_date)->startOfDay();
                $fin = Carbon::parse($offer->end_date)->endOfDay();
                $offer->is_active = now()->between($inicio, $fin);

                return $this->addPrice($offer);
            });

        return response()->json($offers, 200);
    }

    // POST /api/offers (administrador)
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount'   => 'required|numeric|between:1,90',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $offer = Offer::create($data)->load('product:id,name,price,image');

        return response()->json($this->addPrice($offer), 201);
    }

    // PUT /api/offers/{id} (administrador)
    public function update(Request $request, $id)
    {
        $offer = Offer::find($id);

        if (! $offer) {
            return response()->json(['message' => 'Oferta no encontrada.'], 404);
        }

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount'   => 'required|numeric|between:1,90',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $offer->update($data);
        $offer->load('product:id,name,price,image');

        return response()->json($this->addPrice($offer), 200);
    }

    // DELETE /api/offers/{id} (administrador)
    public function destroy($id)
    {
        $offer = Offer::find($id);

        if (! $offer) {
            return response()->json(['message' => 'Oferta no encontrada.'], 404);
        }

        $offer->delete();

        return response()->json(['message' => 'Oferta eliminada.'], 200);
    }
}