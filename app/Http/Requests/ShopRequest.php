<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
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
        $isUpdate = $this->route()->uri == 'api/update-shop';
        
        return [
            'id' => ['sometimes', 'integer'],
            'image' => ['sometimes', 'image','nullable'],
            'name' => ['required', $isUpdate ? Rule::unique('shops')->ignore($this->id) : 'unique:shops'],
            'description' => ['nullable'],
            'shop' => 'required',
            'active' => ['sometimes'],
        ];
      
    }

    protected function prepareForValidation(): void
    {

        $this->merge([
            'shop'    => json_decode($this->shop,true),
        ]);
    }
}
