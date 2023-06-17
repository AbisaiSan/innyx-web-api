<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * GET / products
 * GET / products/:id
 * POST / products
 * PUT/PATCH / products/:id
 * DELETE / products/:id
 */

Route::apiResource('products', \App\Http\Controllers\API\ProductController::class);
Route::apiResource('products.categories', \App\Http\Controllers\API\ProductCategoryController::class)->only('index');
//  Route::controller(\App\Http\Controllers\API\ProductController::class)
//     ->prefix('products')
//     ->group(function() {
//         Route::get('/', 'index');
//         Route::get('/{product}', 'show');
//         Route::post('/', 'store');
//         Route::match(['put', 'patch'], '/{product}', 'update');
//         Route::delete('/{product}', 'destroy');
//  });

