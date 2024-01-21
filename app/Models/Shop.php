<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected $appends = ['shop_image'];

    public function scopeActive($query): void
    {
        $query->where('active', 1);
    }
    protected function shopImage(): Attribute
    {
        $asset =  $this->image ? "/shop/images/".$this->image : "/default_images/shop.png";
        return new Attribute(
            get: fn () => asset("storage".$asset),
        );
    }
    public function products(){
        return $this->hasMany(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
