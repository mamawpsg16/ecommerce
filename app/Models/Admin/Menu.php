<?php

namespace App\Models\Admin;

use App\Models\Admin\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeActive($query): void
    {
        $query->where('active', true);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withPivot('active')->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withPivot('active')->withTimestamps();
    }
}
