<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Prueba de conexión
Route::get('/ping', function () {
    return response()->json([
        'message'  => 'API de Kaboom Express funcionando',
        'database' => DB::connection()->getDatabaseName(),
    ]);
});

// ---------- Públicas ----------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/by-category/{id}', [ProductController::class, 'byCategory']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories-with-products', [CategoryController::class, 'withProducts']);

Route::get('/offers', [OfferController::class, 'index']);
Route::get('/companies', [CompanyController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);

// ---------- Cliente (requieren token) ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// ---------- Administrador (token + rol admin) ----------
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/categories/products-count', [CategoryController::class, 'productsCount']);
});