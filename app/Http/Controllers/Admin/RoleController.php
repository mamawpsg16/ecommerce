<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::latest()->get();

        return response(['roles' => $roles]);
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
                    'name' => ['required', 'unique:roles'],
                    'permissions' => ['required', 'array'],
                    'menu_ids' => ['required', 'array'],
                    'slug' => 'required',
                ]);

                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }

                $data = $validator->validated();
                $data['created_by'] = $email;

                  
                $role = Role::create($data);

                $role->menus()->attach($data['menu_ids']);
              
                $dataToInsert = [];

                foreach ($data['permissions'] as $menu_id => $permissions) {
                    foreach ($permissions as $permission) {
                        $dataToInsert[] = [
                            'menu_id' => $menu_id,
                            'permission_id' => $permission,
                            'created_at' => now(),
                        ];
                    }
                }
                
                DB::table('menu_permissions')->insert($dataToInsert);
                
                return response(['data' => $role], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save role.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $menu_ids = $role->menus->pluck('id');
        $menu_permissions = DB::table('menu_permissions')
        ->select(DB::raw("GROUP_CONCAT(permission_id SEPARATOR ',') as permissions"), "menu_id")
        ->whereIn('menu_id', $menu_ids)
        ->groupBy('menu_id')
        ->get();
        

        return response(['data' => $role, 'menu_permissions' => $menu_permissions, 'menu_ids' => $menu_ids]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        try {
            return DB::transaction(function () use ($request, $role) {
                $email = $request->user()->email;
        
                $validator = Validator::make($request->all(), [
                    'name' => ['required',  Rule::unique('roles')->ignore($request->id)],
                    'permissions' => ['required', 'array'],
                    'menu_ids' => ['required', 'array'],
                ]);
        
                // Validation check
                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }
        
                $data = $validator->validated();
        
                // Detach existing menu relations
                $role->menus()->detach();
                // Delete related menu permissions
                DB::table('menu_permissions')
                    ->whereNotIn('menu_id', $data['menu_ids'])
                    ->delete();

        
                // Update the role with the new data
                $data['updated_by'] = $email;
                $role->update($data);
        
                // Attach new menu relations
                $role->menus()->attach($data['menu_ids']);
        
                // Insert new menu permissions
                $dataToInsert = [];
                foreach ($data['permissions'] as $menu_id => $permissions) {
                    foreach ($permissions as $permission) {
                        $exists =  DB::table('menu_permissions')->where('menu_id', $menu_id)->where('permission_id', $permission)->exists();
                        if(!$exists){
                            $dataToInsert[] = [
                                'menu_id' => $menu_id,
                                'permission_id' => $permission,
                                'created_at' => now(),
                            ];
                        }
                    }
                }
                if($dataToInsert){
                    DB::table('menu_permissions')->insert($dataToInsert);
                }
        
                // Retrieve and return relevant data
                $menu_ids = $role->menus->pluck('id');
                $menu_permissions = DB::table('menu_permissions')
                    ->select(DB::raw("GROUP_CONCAT(permission_id SEPARATOR ',') as permissions"), "menu_id")
                    ->whereIn('menu_id', $menu_ids)
                    ->groupBy('menu_id')
                    ->get();
        
                return response(['data' => $role, 'menu_ids' => $menu_ids, 'menu_permissions' => $menu_permissions]);
            });
        
        } catch (\Exception $e) {
            // Rollback transaction in case of exception
            DB::rollback();
            return response(['error' => 'Failed to save role.' . $e->getMessage()], 500);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try{
            return DB::transaction(function () use($role) {
            
                $role->delete();
                return response(['message' => 'Role deleted successfully!']); 
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to delete role.'], 500);
        }
    }

    public function roles(){
        $roles = Role::select(['name as label', 'id as value'])->active()->latest()->get();

        return response(['roles' => $roles]);
    }
}
