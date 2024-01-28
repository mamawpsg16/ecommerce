<?php
namespace App\Models\Raffle;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function events(){
        return $this->belongsToMany(Event::class);
    } 
    
}
