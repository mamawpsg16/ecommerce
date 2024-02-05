<?php

namespace App\Models\Raffle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    public function scopeActive($query): void
    {
        $query->where('active', 1);
    }

    public function participants(){
        return $this->belongsToMany(Participant::class,  'event_id', 'participant_id')->withTimestamps();
    } 
}
