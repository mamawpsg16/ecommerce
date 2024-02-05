<?php

namespace App\Http\Controllers\Raffle;

use App\Models\Raffle\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\UniqueOrderForEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with(['event'])->latest()->get();
        return response(['items' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'item_image'    => ['sometimes', 'image', 'max:5120'],
                    'name'          => ['required', 'unique:event_items'],
                    'description'   => ['sometimes'],
                    'quantity'      => ['required'],
                    'chance_rate'   => ['required'],
                    'color'         => ['required'],
                    'event_id'      => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }
                
                $data = $validator->validated();

                $item = Item::create($data);
                $this->uploadImage($request, $item);
        
                return response(['item' => $item]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to item.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return response(['data' => $item->load(['event'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateItem(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'id'            => ['required'],
                    'item_image'    => ['sometimes', 'image', 'max:5120'],
                    'name'          => ['required',  Rule::unique('event_items')->ignore($request->id)],
                    'description'   => ['sometimes'],
                    'quantity'      => ['required'],
                    'chance_rate'   => ['required'],
                    'order'         => ['required',new UniqueOrderForEvent($request->event_id, $request->id)],
                    'color'         => ['required'],
                    'active'        => ['required'],
                    'delete_image'  => ['required'],
                    'event_id'      => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }
                
                $data = $validator->validated();

                $item = Item::findOrFail($request->id);

                $item->update($data);

                $this->uploadImage($request, $item, true);

                
                return response(['data' => $item->load(['event'])]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to item.'.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function items(Request $request){
        $event_id = request('event_id');
        $items = Item::select('id','name','chance_rate','quantity','image','color','order')->active()->where('event_id',$event_id)->orderBy('order')->get();

        return response(['items' => $items]);
    }

    public function updateStatus(Request $request){
        try {
            return DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'id'         => ['required'],
                    'status'     => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }

                $data = $validator->validated();

                $item = Item::findOrFail($data['id']);

                $item->update(['active' => $data['status']]);
                return response(['data' => $item ]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to event.'.$e->getMessage()], 500);
        }
    }

    private function uploadImage($request, $item, $edit = false) {
        $file = $request->file('item_image');
        $delete_image = filter_var($request->delete_image, FILTER_VALIDATE_BOOLEAN);
        // DELETE EXISTING IMAGE
        if ($edit && $delete_image) {
            $this->deleteImage($item);
            $item->image = null;
            $item->save();
        }
    
        if ($file) {
            // UPLOAD IMAGE
            $file_name = $file->hashName();
    
            // If editing, delete the existing image
            if ($edit) {
                $this->deleteImage($item);
                $item->image = null;
                $item->save();
            }
    
            $file->storeAs('item/images', $file_name, 'public');
    
            $item->image = $file_name;
            $item->save();
        }
    }
    
    private function deleteImage($item) {
        $path = 'item/images/' . $item->image;
        Storage::disk('public')->delete($path);
    }
    
}
