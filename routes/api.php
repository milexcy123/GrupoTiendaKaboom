<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LatestProductController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UploadController;
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
Route::get('/products-latest', LatestProductController::class);
Route::get('/products/by-category/{id}', [ProductController::class, 'byCategory']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories-with-products', [CategoryController::class, 'withProducts']);

Route::get('/offers', [OfferController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/companies', [CompanyController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);

// ---------- Cliente (requieren token) ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});

// ---------- Administrador (token + rol admin) ----------
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Productos
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Categorías
    Route::get('/categories/products-count', [CategoryController::class, 'productsCount']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Ofertas
    Route::post('/offers', [OfferController::class, 'store']);
    Route::put('/offers/{id}', [OfferController::class, 'update']);
    Route::delete('/offers/{id}', [OfferController::class, 'destroy']);

    // Banners
    Route::post('/banners', [BannerController::class, 'store']);
    Route::put('/banners/{id}', [BannerController::class, 'update']);
    Route::delete('/banners/{id}', [BannerController::class, 'destroy']);

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'summary']);

        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
        Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::put('/orders/{id}/payment', [AdminOrderController::class, 'updatePayment']);

        Route::get('/customers', [CustomerController::class, 'index']);

        Route::get('/offers', [OfferController::class, 'adminIndex']);
        Route::get('/banners', [BannerController::class, 'adminIndex']);
        Route::post('/upload', [UploadController::class, 'store']);

        Route::get('/reports/sales', [ReportController::class, 'sales']);
        Route::get('/reports/top-products', [ReportController::class, 'topProducts']);
        Route::get('/reports/payments', [ReportController::class, 'payments']);
    });
});