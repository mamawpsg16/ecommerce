<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Admin\Menu;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $menus = Menu::latest()->get();

        return response(['menus' => $menus]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $email = $request->user()->email;

                $validator = Validator::make($request->all(), [
                    'name'  => ['required', 'unique:menus'],
                    'url'   => ['required', 'unique:menus'],
                    'icon'  => 'sometimes',
                    'order' => 'required',
                ]);
                if ($validator->fails()) {
                    return response(['status' => 404, 'errors' => $validator->errors()]);
                }
                $data = $validator->validated();
                $data['created_by'] = $email;
                $menu = Menu::create($data);

                return response(['data' => $menu], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save menu.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
  

        return response(['data' => $menu]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        try {
            return DB::transaction(function () use ($request, $menu) {
                $email = $request->user()->email;

                $validator = Validator::make($request->all(), [
                    'name'    => ['required',  Rule::unique('menus')->ignore($request->id)],
                    'url'     => ['required', Rule::unique('menus')->ignore($request->id)],
                    'icon'    => 'sometimes',
                    'order'   => 'required',
                    'active'  => 'required',
                ]);

                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }

                $data = $validator->validated();
                $data['updated_by'] = $email;
                $menu->update($data);

                return response(['data' => $menu]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save menu.'.$e->getMessage()], 500);
        }
    }
    
    public function menus(){
        $menus = Menu::select(['name as label', 'id as value'])->active()->latest()->get();

        return response(['menus' => $menus]);
    }
}
