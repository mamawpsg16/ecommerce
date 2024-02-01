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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->order = self::max('order') + 1;
        });
    }
    
    public function scopeActive($query): void
    {
        $query->where('active', 1);
    }

    protected function itemImage(): Attribute
    {
        $asset =  $this->image ? "/item/images/".$this->image : "/default_images/product.png";
        return new Attribute(
            get: fn () => asset("storage".$asset),
        );
    }
}
