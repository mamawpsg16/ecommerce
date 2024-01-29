<?php

namespace App\Models\Raffle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'event_items';
    
    public function scopeActive($query): void
    {
        $query->where('active', 1);
    }
}
