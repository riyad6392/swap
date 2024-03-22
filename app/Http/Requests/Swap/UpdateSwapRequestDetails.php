<?php

namespace App\Http\Requests\Swap;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSwapRequestDetails extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
}
