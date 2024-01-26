<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Admin\Permission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissions = Permission::latest()->get();

        return response(['permissions' => $permissions]);
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
                    'name' => ['required', 'unique:permissions'],
                    'slug' => 'required',
                ]);

                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }

                $data = $validator->validated();
                $data['created_by'] = $email;
                $permission = Permission::create($data);

                return response(['data' => $permission], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save permission.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
  

        return response(['data' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        try {
            return DB::transaction(function () use ($request, $permission) {
                $email = $request->user()->email;
                $validator = Validator::make($request->all(), [
                    'name' => ['required',  Rule::unique('permissions')->ignore($request->id)],
                    'active'  => 'required',
                ]);
                
                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }
                $data = $validator->validated();
                $data['updated_by'] = $email;
                $permission->update($data);

                return response(['data' => $permission]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save permission.'.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        try{
            return DB::transaction(function () use($permission) {
            
                $permission->delete();
                return response(['message' => 'Permission deleted successfully!']); 
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to delete permission.'], 500);
        }
    }

    public function permissions(){
        $permissions = Permission::select(['name as label', 'id as value'])->active()->latest()->get();

        return response(['permissions' => $permissions]);
    }
}
