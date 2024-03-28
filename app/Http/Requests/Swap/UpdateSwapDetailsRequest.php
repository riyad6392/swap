<?php

namespace App\Http\Requests\Swap;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSwapDetailsRequest extends FormRequest
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
        $rules = [
            'define_type' => 'required|string|in:request_product,exchange_product',
            'deleted_details_id' => 'nullable|array',
        ];
        if ($this->define_type == 'exchange_product') {
            $rules = [
//                'define_type' => 'required|string|in:request_product,exchange_product',
                'exchange_product.*.product_id' => 'required|integer',
                'exchange_product.*.variation_id' => 'required|integer',
                'exchange_product.*.variation_size' => 'required',
                'exchange_product.*.variation_color' => 'required',
                'exchange_product.*.variation_quantity' => 'nullable|numeric',
                'exchange_product.*.discount_end_date' => 'nullable|date',
            ];
        } else {
            $rules = [
//                'define_type' => 'required|string|in:request_product,exchange_product',
                'request_product.*.product_id' => 'required|integer',
                'request_product.*.variation_id' => 'required|integer',
                'request_product.*.variation_size' => 'required',
                'request_product.*.variation_color' => 'required',
                'request_product.*.variation_quantity' => 'nullable|numeric',
                'request_product.*.discount_end_date' => 'nullable|date',
            ];
        }
        return $rules;
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

            'request_product.*.product_id.required' => 'Product id is required',
            'request_product.*.variation_id.required' => 'Variation id is required',
            'request_product.*.variation_size.required' => 'Variation size is required',
            'request_product.*.variation_color.required' => 'Variation color is required',
            'request_product.*.variation_quantity.numeric' => 'Variation quantity must be a number',
            'request_product.*.discount_end_date.date' => 'Discount end date must be a date',

        ];
    }
}
