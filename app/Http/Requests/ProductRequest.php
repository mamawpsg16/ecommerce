<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $isUpdate = $this->route()->uri == 'api/update-product';
        
        return [
            'id' => ['sometimes', 'integer'],
            'image' => ['sometimes', 'image','nullable'],
            'product' => 'required',
            'name' => ['required', $isUpdate ? Rule::unique('products')->ignore($this->id) : 'unique:products'],
            'description' => ['nullable'],
            'price' => 'required',
            'quantity' => 'required',
            'categories' => ['required', 'array'],
            'shop_id' => ['required'],
        ];
      
    }

    protected function prepareForValidation(): void
    {

        $this->merge([
            'product'    => json_decode($this->product,true),
            'categories' => json_decode($this->categories),
        ]);

    }
}
