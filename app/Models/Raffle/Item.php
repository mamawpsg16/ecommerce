<?php

namespace App\Models\Raffle;

use Illuminate\Support\Facades\DB;
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
            // Assuming you have 'event_id' and 'order' columns in your table
            $event_id = $model->event_id;

            // Get the maximum order for the given event_id
            $maxOrder = DB::table('event_items')
                ->where('event_id', $event_id)
                ->max('order');

            // Set the order for the new record
            $model->order = $maxOrder + 1;
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

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
