<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\App\AuthenticationController;

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

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', [AuthenticationController::class, 'tokenVerification'])->middleware('user.authenticated');
    Route::post('/logout',[AuthenticationController::class,'logout']); 

    Route::apiResources([
        'shops' => ShopController::class,
        'products' => ProductController::class,
        'categories' => CategoryController::class,
        'home' => HomeController::class,
    ]);

    Route::post('update-product', [ProductController::class, 'update']);
    Route::post('update-shop', [ShopController::class, 'update']);
    Route::get('get-categories', [CategoryController::class, 'categories']);
    Route::get('get-shops', [ShopController::class, 'shops']);
    Route::get('shop-details/{shop:slug}', [ShopController::class, 'shopDetails']);
    Route::get('product-details/{product:slug}', [ProductController::class, 'productDetails']);
    Route::post('product-add-to-cart', [ProductController::class, 'addToCart']);
    Route::get('search-product-existence', [ProductController::class, 'searchProductExistence']);

});