<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeActive($query): void
    {
        $query->where('active', true);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_permissions', 'permission_id', 'menu_id')
            ->withPivot('active')
            ->withTimestamps();
    }

    
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withPivot('active')->withTimestamps();
    }
}
