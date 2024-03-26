<?php

namespace App\Http\Requests\Swap;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreSwapRequestDetails extends FormRequest
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
            'swap_id' => 'required|exists:swaps,id',
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'required|exists:product_variations,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'swap_id.required' => 'Swap id is required',
            'swap_id.exists' => 'Swap id does not exist',
            'user_id.required' => 'User id is required',
            'user_id.exists' => 'User id does not exist',
            'product_id.required' => 'Product id is required',
            'product_id.exists' => 'Product id does not exist',
            'product_variation_id.required' => 'Product variation id is required',
            'product_variation_id.exists' => 'Product variation id does not exist',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be an integer',
            'quantity.min' => 'Quantity must be at least 1',
            'unit_price.required' => 'Unit price is required',
            'unit_price.numeric' => 'Unit price must be a number',
            'unit_price.min' => 'Unit price must be at least 0',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0',
            'commission.required' => 'Commission is required',
            'commission.numeric' => 'Commission must be a number',
            'commission.min' => 'Commission must be at least 0',
        ];
    }
}
