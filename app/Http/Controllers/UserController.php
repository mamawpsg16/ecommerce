<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CartItem;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cartItems(Request $request){
        $user_id = $request->user()->id;

        $items = CartItem::with(['product' => function($q){
            $q->select('id','name', 'slug', 'image', 'description', 'price', 'quantity', 'shop_id');
        }, 'product.shop' => function($q){
            $q->select('id','name', 'slug',);
        }])->where('user_id', $user_id)->get();

        return response(['items' => $items]);
    }

    public function deleteCartItem(Request $request, CartItem $cart_item){
        $cart_item->delete();
            
        $user_id = $request->user()->id;
        $cart_items_count = CartItem::where('user_id', $user_id)->count();

        return response(['message' => 'Cart item deleted', 'cart_items_count' => $cart_items_count], 200);
    }
}
