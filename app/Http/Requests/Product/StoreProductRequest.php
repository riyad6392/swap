<?php

namespace App\Http\Requests\Product;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    use ValidationErrorMessageTrait;
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
        return [
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id'=>'required|exists:brands,id',
            'product_images' => 'required',
            'is_publish' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'description.required' => 'Product description is required',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Product price must be a number',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Category does not exist',
            'user_id.required' => 'User is required',
            'user_id.exists' => 'User does not exist',
            'user_id.integer' => 'User must be an integer',
            'product_images.required' => 'Product image is required',
            'product_images.array' => 'Product image must be an array'
        ];
    }
}
