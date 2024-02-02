<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Models\Admin\Menu;
use App\Models\MenuPermission;
use App\Models\MenuRolePermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeActive($q){
        $q->where('active',1 );
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withPivot('active')->where('permissions.active',1)->withTimestamps();
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class)->withPivot('active')->where('menus.active',1)->withTimestamps();
    }
 
}
