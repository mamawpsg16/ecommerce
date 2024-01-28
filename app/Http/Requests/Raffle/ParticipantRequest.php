<?php

namespace App\Http\Requests\Raffle;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->route()->uri == 'api/update-participant';
        
        return [
            'id' => ['sometimes', 'integer'],
            'name' => ['required', $isUpdate ? Rule::unique('participants')->ignore($this->id) : 'sometimes'],
            'address' => ['required'],
            'email' => ['required', 'email'],
            'personal_phone_number' => ['required'],
            'birth_date' => 'required',
            'company' => 'required',
            'company_address' => 'required',
            'company_phone_number' => ['nullable'],
            'industry' => ['required'],
            'position' => ['required'],
            'event' => ['required'],
            'age' => ['required', 'numeric'],
        ];
      
    }
 
}
