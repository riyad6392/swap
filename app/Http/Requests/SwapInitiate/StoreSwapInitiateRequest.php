<?php

namespace App\Http\Requests\SwapInitiate;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreSwapInitiateRequest extends FormRequest
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
            'exchanged_user_id' => 'required|integer|exists:users,id',
            'products' => 'array|required',
            'products.*.product_id' => 'required|integer|exists:products,id',
//            'products.*.product_variation_id' => 'integer|exists:product_variations,id',
//            'products.*.quantity' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'exchanged_user_id.required' => 'The exchanged user field is required.',
            'exchanged_user_id.integer' => 'The exchanged user field must be an integer.',
            'exchanged_user_id.exists' => 'The selected exchanged user is invalid.',
            'products.required' => 'The products field is required.',
            'products.array' => 'The products field must be an array.',
            'products.*.product_id.required' => 'The product id field is required.',
            'products.*.product_id.integer' => 'The product id field must be an integer.',
            'products.*.product_id.exists' => 'The selected product id is invalid.'
        ];

    }
}
