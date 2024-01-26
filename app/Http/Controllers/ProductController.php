<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
       
        $products = Product::select('products.id', 'products.name', 'products.description', 'shops.name as shop_name', 'products.price', 'products.quantity', 'products.active', 'products.created_at', 'products.updated_at',DB::raw('GROUP_CONCAT(categories.name SEPARATOR ", ") as categories'))
            ->Join('shops', 'products.shop_id', '=', 'shops.id')
            ->Join('category_product', 'products.id', '=', 'category_product.product_id')
            ->Join('categories', 'category_product.category_id', '=', 'categories.id')
            // ->where('products.active', 1)
            ->groupBy('products.id') // Group by the product's primary key
            ->latest()
            ->get();

        return response(['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $data = $request->validated();

                $product = Product::create($data['product']);

                $file = $request->file('image');
                if($file){
                    $file_name = $file->hashName();
                    
                    $file->storeAs('product/images', $file_name, 'public');
        
                    $product->image = $file_name;
        
                    $product->save();
                }
    
                $product->categories()->attach($data['categories']);
             
                return response(['data' => $product], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save product.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $category_ids = $product->categories()->pluck('category_id');

        $categories = Category::select(['name as label', 'id as value'])->whereIn('id',$category_ids)->get();
        return response(['data' => $product->load(['shop']), 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                $product = Product::findOrFail($request->id);

                $product->update($data['product']);

                $file = $request->file('image');

                if($file){
                    $file_name = $file->hashName();
                    $path = 'product/images';

                    $asset =  asset("storage"."/".$path.$product->image);
               
                    Storage::disk('public')->delete($asset);

                    $file->storeAs($path, $file_name, 'public');
        
                    $product->image = $file_name;
        
                    $product->save();
                }
    
                $product->categories()->detach();

                $product->categories()->attach($data['categories']);
                
                $category_ids = $product->categories()->pluck('category_id');

                $categories = Category::select(['name as label', 'id as value'])->whereIn('id',$category_ids)->get();
                
                return response(['data' => $product->load(['shop']), 'categories' => $categories]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save product.'.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{
            return DB::transaction(function () use($product) {
                if($product->image){
                    $asset =  "storage/product/images/".$product->image;
                    
                    Storage::disk('public')->delete($asset);
                }
            
                $product->categories()->detach();
                $product->delete();
                return response(['message' => 'Product deleted successfully!']); 
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to delete product.'], 500);
        }
    }

    public function productDetails(Product $product){
         
        return response(['details' => $product->load(['shop','categories'])]);
    }

    public function addToCart(Request $request){

        try{
            return DB::transaction(function () use($request) {
                $validator = Validator::make($request->all(),[
                    'product_id' => ['required', 'integer'],
                    'quantity'   => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }
                
                $user_id = $request->user()->id;
                
                $data = $validator->validated();
                
                $product = Product::find($data['product_id']);
                $data['user_id'] = $user_id;
                CartItem::create($data);

                if ($product) {
                    $newQuantity = $product->quantity - $data['quantity'];
                
                    // Ensure the quantity doesn't go below 0
                    $newQuantity = max(0, $newQuantity);
                
                    $product->update(['quantity' => $newQuantity]);
                }

                $cart_items_count = CartItem::where('user_id', $user_id)->count();

                return response(['message' => 'Added to cart', 'data' => $product->load(['shop']), 'cart_items_count' => $cart_items_count]);
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to add item to cart.'. $e->getMessage()], 500);
        }
    }

    public function searchProductExistence(Request $request){
        $products = Product::with('shop')->where('name', 'like', '%' . request('search') . '%')->get();
        return response(['products' => $products]);
    }
}
