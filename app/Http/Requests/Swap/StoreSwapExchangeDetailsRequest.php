<?php

namespace App\Http\Requests\Swap;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class StoreSwapExchangeDetailsRequest extends FormRequest
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
            'define_type' => 'required|string|in:exchange_product',
            'exchange_product.*.product_id' => 'required|integer',
            'exchange_product.*.variation_id' => 'required|integer',
            'exchange_product.*.variation_size' => 'required',
            'exchange_product.*.variation_color' => 'required',
            'exchange_product.*.variation_quantity' => 'nullable|numeric',
            'exchange_product.*.discount_end_date' => 'nullable|date',
        ];


    }

    public function messages(): array
    {
        return [
            'define_type.required' => 'Define type is required',
            'define_type.string' => 'Define type must be a string',
            'define_type.in' => 'Define type must be either request_product or exchange_product',

            'exchange_product.*.product_id.required' => 'Product id is required',
            'exchange_product.*.variation_id.required' => 'Variation id is required',
            'exchange_product.*.variation_size.required' => 'Variation size is required',
            'exchange_product.*.variation_color.required' => 'Variation color is required',
            'exchange_product.*.variation_quantity.numeric' => 'Variation quantity must be a number',
            'exchange_product.*.discount_end_date.date' => 'Discount end date must be a date',
        ];
    }
}
