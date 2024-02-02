<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Menu;
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
                    'menu_permissions' => ['required', 'array'],
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

                array_map(function($permission_ids, $menu_id) use($role){
                    foreach($permission_ids as $id){
                        $menu_permission_id = DB::table('menu_permission')->insertGetId([
                            'menu_id' => $menu_id,
                            'permission_id' => $id,
                        ]);

                        DB::table('menu_role_permission')->insert([
                            'role_id' => $role->id,
                            'menu_permission_id' => $menu_permission_id,
                            'created_at' => now(),
                        ]);
                    }
                },$data['menu_permissions'], array_keys($data['menu_permissions']));
                
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
        // $menu_permission_ids  = DB::table('menu_role_permission')
        //             ->where('role_id', $role->id)
        //             ->pluck('id');
        $menu_permissions= DB::table('menu_permission as mp')
        ->select('mp.menu_id', 'mrp.role_id', 'mp.id', DB::raw('GROUP_CONCAT(mp.permission_id SEPARATOR ",") as permissions'))
        ->join('menu_role_permission as mrp', 'mp.id', '=', 'mrp.menu_permission_id')
        ->join('menu_role as mr', 'mp.menu_id', '=', 'mr.menu_id')
        ->where('mrp.role_id', $role->id)
        ->groupBy('mp.menu_id', 'mrp.role_id', 'mp.id')
        ->get();
        dd($menu_permissions);
        return response(['data' => $role, 'menu_permissions' => $menu_permissions, 'menu_ids' => $menu_ids, 'menu_permission_ids' => null]);
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
                    // 'menu_permission_ids' => ['required', 'array'],
                ]);
        
                // Validation check
                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }
        
                $data = $validator->validated();
                // Detach existing menu relations
                $role->menus()->detach();
                // Delete related menu permissions
                DB::table('menu_permission')
                    ->whereIn('id', $data['menu_ids'])
                    ->delete();

                DB::table('menu_role_permission')
                    ->where('role_id', $role->id)
                    // ->whereIn('menu_id', $data['menu_permission_ids'])
                    ->delete();
                    
                    // Update the role with the new data
                $data['updated_by'] = $email;
                $role->update($data);
                    
                // Attach new menu relations
                $role->menus()->attach($data['menu_ids']);
        
                array_map(function($permission_ids, $menu_id) use($role){
                    foreach($permission_ids as $id){
                        $menu_permission_id = DB::table('menu_permission')->insertGetId([
                            'menu_id' => $menu_id,
                            'permission_id' => $id,
                        ]);

                        DB::table('menu_role_permission')->insert([
                            'role_id' => $role->id,
                            'menu_permission_id' => $menu_permission_id,
                            'created_at' => now(),
                        ]);
                    }
                },$data['permissions'], array_keys($data['permissions']));

                // Retrieve and return relevant data
                $menu_ids = $role->menus->pluck('id');
       
                $menu_permissions = DB::table('menu_permission as mp')
                ->select('mp.id','mp.menu_id', 'mrp.role_id', DB::raw('GROUP_CONCAT(mp.permission_id SEPARATOR ",") as permissions'))
                ->join('menu_role_permission as mrp', 'mp.id', '=', 'mrp.menu_permission_id')
                ->join('menu_role as mr', 'mp.menu_id', '=', 'mr.menu_id')
                ->where('mrp.role_id', $role->id)
                ->groupBy('mp.menu_id', 'mrp.role_id', 'mp.id')
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
