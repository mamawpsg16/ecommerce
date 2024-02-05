<?php

namespace App\Rules;

use Closure;
use App\Models\Raffle\Item;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueOrderForEvent implements ValidationRule
{
    private $eventId;
    private $ignoreId;
    public function __construct($eventId, $ignoreId)
    {
        $this->eventId = $eventId;
        $this->ignoreId = $ignoreId;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isExists = Item::where('id', '!=', $this->ignoreId)
        ->where('event_id', $this->eventId)
        ->where($attribute, $value)
        ->exists();

       if($isExists){
        $fail('The :attribute already exists.');
       }

    }
}
