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
            'exchange_product' => 'required|array',
            'exchange_product.*.product_id' => 'required|integer|exists:products,id',
            'exchange_product.*.variation_id' => 'required|integer|exists:product_variations,id',
            'exchange_product.*.variation_size_id' => 'required|integer|exists:sizes,id',
            'exchange_product.*.variation_color_id' => 'required|integer|exists:colors,id',
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
            'exchange_product.required' => 'Exchange product is required',
            'exchange_product.array' => 'Exchange product must be an array',
            'exchange_product.*.product_id.required' => 'Product id is required',
            'exchange_product.*.product_id.integer' => 'Product id must be a number',
            'exchange_product.*.product_id.exists' => 'Product id does not exist',
            'exchange_product.*.variation_id.required' => 'Variation id is required',
            'exchange_product.*.variation_size_id.required' => 'Variation size is required',
            'exchange_product.*.variation_size_id.integer' => 'Variation size must be a number',
            'exchange_product.*.variation_size_id.exists' => 'Variation size does not exist',
            'exchange_product.*.variation_color_id.required' => 'Variation color is required',
            'exchange_product.*.variation_color_id.integer' => 'Variation color must be a number',
            'exchange_product.*.variation_color_id.exists' => 'Variation color does not exist',
            'exchange_product.*.variation_quantity.numeric' => 'Variation quantity must be a number',
            'exchange_product.*.discount_end_date.date' => 'Discount end date must be a date',
        ];
    }
}
