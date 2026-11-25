<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerController extends Controller
{
    // GET /api/banners (solo los activos, para la página de inicio)
    public function index()
    {
        $banners = Banner::where('status', 'active')
            ->orderByDesc('id')
            ->get();

        return response()->json($banners, 200);
    }
}