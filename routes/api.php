<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Raffle\EventController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\App\AuthenticationController;
use App\Http\Controllers\Admin\UserController as AdminController;
use App\Http\Controllers\Raffle\ItemController;
use App\Http\Controllers\Raffle\ParticipantController;

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
    
    Route::get('/get-partials',[AuthenticationController::class,'getPartials']); 

    Route::apiResources([
        'shops' => ShopController::class,
        'products' => ProductController::class,
        'home' => HomeController::class,
    ]);

    Route::group(['prefix' => 'admin'], function(){
        
        Route::apiResources([
            'categories' => CategoryController::class,
            'roles' => RoleController::class,
            'permissions' => PermissionController::class,
            'menus' => MenuController::class,
            'users' => AdminController::class,
        ]);

        Route::get('get-permissions', [PermissionController::class, 'permissions']);
        Route::get('get-menus', [MenuController::class, 'menus']);
        Route::get('get-categories', [CategoryController::class, 'categories']);
        Route::get('get-roles', [RoleController::class, 'roles']);
        Route::get('get-menu-permissions', [AdminController::class, 'menuPermissions']);
        Route::get('get-users', [AdminController::class, 'users']);
    });

    Route::post('update-product', [ProductController::class, 'update']);
    Route::post('update-shop', [ShopController::class, 'update']);
    Route::get('get-shops', [ShopController::class, 'shops']);
    Route::get('shop-details/{shop:slug}', [ShopController::class, 'shopDetails']);
    Route::get('product-details/{product:slug}', [ProductController::class, 'productDetails']);
    Route::post('product-add-to-cart', [ProductController::class, 'addToCart']);
    Route::get('search-product-existence', [ProductController::class, 'searchProductExistence']);
    Route::get('cart-items', [UserController::class, 'cartItems']);
    Route::delete('cart-item/{cart_item}', [UserController::class, 'deleteCartItem']);


    Route::group(['prefix' => 'raffle'], function(){
        Route::get('get-events', [EventController::class, 'events']);
    
        Route::apiResources([
            'participants' => ParticipantController::class,
            'items'    => ItemController::class,
            // 'participants' => EventController::class,
        ]);

        Route::get('get-items', [ItemController::class, 'items']);


    });

});