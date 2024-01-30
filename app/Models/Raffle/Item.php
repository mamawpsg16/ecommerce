<?php

namespace App\Models\Raffle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;
    
    protected $appends = ['item_image'];

    protected $guarded = ['id'];
    protected $table = 'event_items';
    
    public function scopeActive($query): void
    {
        $query->where('active', 1);
    }


    protected function itemImage(): Attribute
    {
        $asset =  $this->image ? "/event_items/".$this->image : "";
        return new Attribute(
            get: fn () => asset("storage".$asset),
        );
    }
}
