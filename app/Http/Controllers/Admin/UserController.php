<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = $request->user()->id;
        $users = User::with(['roles'=> function($q){
            $q->select('roles.id','name');
        }])->latest()->where('id', '!=', $id)->get();
        return response(['users' => $users]);
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
                    'users'  => ['required', 'array'],
                    'roles'  => ['required', 'array'],
                    'menu_permissions'  => ['required', 'array'],
                ]);
                
                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }
                
                $data = $validator->validated();
                $dataToInsert = [];
                foreach ($data['users'] as $user_id) {
                    $user = User::findOrFail($user_id);
                    $user->update([
                        'updated_by' => $email,
                        'updated_at' => now()
                    ]);

                    $user->roles()->attach($data['roles']);
                    foreach ($data['menu_permissions'] as $value) {
                        $exists =  DB::table('user_menu_permissions')->where('user_id', $user_id)->where('menu_permission_id', $value['menu_permission_id'])->exists();
                            if(!$exists){
                                $dataToInsert[] = [
                                    'user_id' => $user_id,
                                    'menu_permission_id' => $value['menu_permission_id'],
                                    'created_at' => now(),
                                ];
                            }
                    }
                    if($dataToInsert){
                        DB::table('user_menu_permissions')->insert($dataToInsert);
                    }
                }

                return response(['message' => 'Success' ], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save user.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $roleIds = $user->roles->pluck('id');
        $menuPermissionIds = DB::table('user_menu_permissions')->where('user_id',$user->id)->pluck('menu_permission_id');
        
        unset($user->roles);

        $user->roles = DB::table('roles')->select('name as label', 'id as value')->whereIn('id', $roleIds)->get();
        $user->menu_permissions = DB::table('menu_permissions')->selectRaw('CONCAT(menus.name, " - ", permissions.name) as label')
        ->addSelect('menu_permissions.id as value', 'menu_role.role_id')
        ->join('menu_role', 'menu_permissions.menu_id', '=', 'menu_role.menu_id')
        ->join('menus', 'menu_permissions.menu_id', '=', 'menus.id')
        ->join('permissions', 'menu_permissions.permission_id', '=', 'permissions.id')
        ->whereIn('menu_permissions.id',$menuPermissionIds)
        ->get();

        $menuPermissions = DB::table('menu_permissions')->selectRaw('CONCAT(menus.name, " - ", permissions.name) as label')
            ->addSelect('menu_permissions.id as value', 'menu_role.role_id')
            ->join('menu_role', 'menu_permissions.menu_id', '=', 'menu_role.menu_id')
            ->join('menus', 'menu_permissions.menu_id', '=', 'menus.id')
            ->join('permissions', 'menu_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('menu_role.role_id', $roleIds)
            ->get();

        return response(['data' => $user, 'menuPermissions' => $menuPermissions]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            return DB::transaction(function () use ($request, $user) {
                $email = $request->user()->email;
                $validator = Validator::make($request->all(), [
                    'roles'  => ['required', 'array'],
                    'menu_permissions'  => ['required', 'array'],
                    'menu_permission_ids'  => ['required', 'array'],
                    'active'  => 'required',
                ]);
                
                if ($validator->fails()) {
                    return ['status' => 404, 'errors' => $validator->errors()];
                }
                $data = $validator->validated();

                $user->roles()->detach();

                $user->roles()->attach($data['roles']);

                $data['updated_by'] = $email;

                $user->update([
                    'updated_by' => $email,
                    'updated_at' => now(),
                    'active' => $data['active'],
                ]);
                
                $roleIds = $user->roles->pluck('id');
                DB::table('user_menu_permissions')->whereNotIn('menu_permission_id',$data['menu_permission_ids'])->delete();

                $dataToInsert = [];
                foreach ($data['menu_permissions'] as $data) {
                    $exists =  DB::table('user_menu_permissions')->where('user_id', $data['user_id'])->where('menu_permission_id', $data['menu_permission_id'])->exists();
                        if(!$exists){
                            $dataToInsert[] = [
                                'user_id' => $data['user_id'],
                                'menu_permission_id' => $data['menu_permission_id'],
                                'created_at' => now(),
                            ];
                        }
                }
                if($dataToInsert){
                    DB::table('user_menu_permissions')->insert($dataToInsert);
                }

                $menuPermissionIds = DB::table('user_menu_permissions')->where('user_id',$user->id)->pluck('menu_permission_id');

              
                
                
                unset($user->roles);
                
                $user->roles = DB::table('roles')->select('name as label', 'id as value')->whereIn('id', $roleIds)->get();
                $user->menu_permissions = DB::table('menu_permissions')->selectRaw('CONCAT(menus.name, " - ", permissions.name) as label')
                ->addSelect('menu_permissions.id as value', 'menu_role.role_id')
                ->join('menu_role', 'menu_permissions.menu_id', '=', 'menu_role.menu_id')
                ->join('menus', 'menu_permissions.menu_id', '=', 'menus.id')
                ->join('permissions', 'menu_permissions.permission_id', '=', 'permissions.id')
                ->whereIn('menu_permissions.id',$menuPermissionIds)
                ->get();

                return response(['data' => $user]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save user.'.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try{
            return DB::transaction(function () use($user) {
            
                $user->delete();
                return response(['message' => 'User deleted successfully!']); 
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to delete user.'], 500);
        }
    }

    public function users(Request $request){
        $id = $request->user()->id;
        $users = User::select(['name as label', 'id as value'])->where('id', '!=', $id)->active()->latest()->get();

        return response(['users' => $users]);
    }

    public function menuPermissions(Request $request){
        $roleIds = $request->input('role_ids') ? $request->input('role_ids')  : []; 
        $menuPermissions = DB::table('menu_permissions')->selectRaw('CONCAT(menus.name, " - ", permissions.name) as label')
            ->addSelect('menu_permissions.id as value', 'menu_role.role_id')
            ->join('menu_role', 'menu_permissions.menu_id', '=', 'menu_role.menu_id')
            ->join('menus', 'menu_permissions.menu_id', '=', 'menus.id')
            ->join('permissions', 'menu_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('menu_role.role_id', $roleIds)
            ->get();
    
        return response(['menu_permissions' => $menuPermissions ]);
    }
}
