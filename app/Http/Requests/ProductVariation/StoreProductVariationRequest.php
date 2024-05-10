<?php

namespace App\Http\Requests\ProductVariation;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariationRequest extends FormRequest
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
            'variations' => 'required|array',
            'variations.*.size_id' => 'required|integer|exists:sizes,id',
            'variations.*.color_id' => 'required|integer|exists:colors,id',
            'variations.*.unit_price' => 'required|numeric',
            'variations.*.stock' => 'required|integer',
            'variations.*.discount' => 'nullable|numeric',
            'variations.*.quantity' => 'required|integer',
            'variations.*.discount_type' => 'nullable|in:percentage,flat',
            'variations.*.discount_start_date' => 'nullable|date',
            'variations.*.discount_end_date' => 'nullable|date',
            'variations.*.variant_images' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'variations.required' => 'Variations are required',
            'variations.array' => 'Variations must be an array',
            'variations.*.size_id.integer' => 'Size must be an integer',
            'variations.*.size_id.exists' => 'Size does not exist',
            'variations.*.color_id.integer' => 'Color must be an integer',
            'variations.*.color_id.exists' => 'Color does not exist',
            'variations.*.unit_price.required' => 'Unit Price is required',
            'variations.*.unit_price.numeric' => 'Unit Price must be a number',
            'variations.*.stock.required' => 'Stock is required',
            'variations.*.stock.integer' => 'Stock must be an integer',
            'variations.*.discount.numeric' => 'Discount must be a number',
            'variations.*.quantity.required' => 'Quantity is required',
            'variations.*.quantity.integer' => 'Quantity must be an integer',
            'variations.*.discount_type.string' => 'Discount type must be a string',
            'variations.*.discount_start_date.date' => 'Discount start date must be a date',
            'variations.*.discount_end_date.date' => 'Discount end date must be a date',
            'variations.*.variant_images.array' => 'Variant images must be an array',
        ];
    }
}
