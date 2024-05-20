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
            'products.*.product_variation_id' => 'integer|exists:product_variations,id',
            'products.*.quantity' => 'required|integer',
        ];
    }
}
