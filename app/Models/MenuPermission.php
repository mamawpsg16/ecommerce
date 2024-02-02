<?php

namespace App\Models;

use App\Models\Admin\Menu;
use App\Models\Admin\Permission;
use App\Models\MenuRolePermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuPermission extends Model
{
    use HasFactory;

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function rolePermissions()
    {
        return $this->hasMany(MenuRolePermission::class);
    }
}
