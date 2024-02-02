<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ShopRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = $request->user()->id;

        $shops = Shop::where('user_id', $user_id)
            ->latest()
            ->get();

        return response(['shops' => $shops]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $user_id = $request->user()->id;

                $data = $request->validated();
                $data['shop']['user_id'] = $user_id;

                $shop = Shop::create($data['shop']);

                $file = $request->file('image');
                if($file){
                    $file_name = $file->hashName();
                    
                    $file->storeAs('shop/images', $file_name, 'public');
        
                    $shop->image = $file_name;
        
                    $shop->save();
                }
    
                return response(['data' => $shop], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save shop.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
  

        return response(['data' => $shop]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                $shop = Shop::findOrFail($request->id);
                $shop->update($data['shop']);

                $file = $request->file('image');

                if($file){
                    $file_name = $file->hashName();
                    $path = 'shop/images';

                    $asset =  asset("storage"."/".$path.$shop->image);
               
                    Storage::disk('public')->delete($asset);

                    $file->storeAs($path, $file_name, 'public');
        
                    $shop->image = $file_name;
        
                    $shop->save();
                }
    
             
                return response(['data' => $shop]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save shop.'.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        try{
            return DB::transaction(function () use($shop) {
                if($shop->image){
                    $asset =  "storage/shop/images/".$shop->image;
                    
                    Storage::disk('public')->delete($asset);
                }
            
                $shop->delete();
                return response(['message' => 'Shop deleted successfully!']); 
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to delete shop.'], 500);
        }
    }

    public function shops(){
    
        $shops = Shop::select(['name as label', 'id as value'])->active()->latest()->get();

        return response(['shops' => $shops]);
    }

    public function shopDetails(Request $request, Shop $shop){

        $products = Product::select('id', 'name', 'image', 'description', 'price', 'quantity', 'created_at', 'updated_at', 'slug')->where('shop_id', $shop->id)->active()->latest()->get();
       
        return response(['products' => $products, 'shop' => $shop]);
    }
}
