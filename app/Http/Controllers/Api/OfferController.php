<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;

class OfferController extends Controller
{
    // GET /api/offers (solo ofertas vigentes, con el precio ya descontado)
    public function index()
    {
        $offers = Offer::with('product:id,name,price,image,stock,status')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->get()
            ->map(function ($offer) {
                $precio = (float) $offer->product->price;
                $offer->final_price = round($precio * (1 - $offer->discount / 100), 2);

                return $offer;
            });

        return response()->json($offers, 200);
    }
}