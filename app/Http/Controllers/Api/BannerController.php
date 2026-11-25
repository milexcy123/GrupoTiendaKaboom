<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

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

    // GET /api/admin/banners (todos)
    public function adminIndex()
    {
        $banners = Banner::orderByDesc('id')->get();

        return response()->json($banners, 200);
    }

    // POST /api/banners (administrador)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'  => 'required|string|max:255',
            'image'  => 'nullable|string|max:255',
            'link'   => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $banner = Banner::create($data);

        return response()->json($banner->fresh(), 201);
    }

    // PUT /api/banners/{id} (administrador)
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return response()->json(['message' => 'Banner no encontrado.'], 404);
        }

        $data = $request->validate([
            'title'  => 'sometimes|required|string|max:255',
            'image'  => 'nullable|string|max:255',
            'link'   => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $banner->update($data);

        return response()->json($banner, 200);
    }

    // DELETE /api/banners/{id} (administrador)
    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return response()->json(['message' => 'Banner no encontrado.'], 404);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner eliminado.'], 200);
    }
}