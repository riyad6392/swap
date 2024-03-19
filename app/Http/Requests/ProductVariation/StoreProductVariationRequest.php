<?php

namespace App\Http\Requests\ProductVariation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductVariationRequest extends FormRequest
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
        return [
            'variations' => 'required|array',
            'variations.*.size' => 'nullable|string',
            'variations.*.color' => 'nullable|string',
            'variations.*.price' => 'required|numeric',
            'variations.*.stock' => 'required|integer',
            'variations.*.discount' => 'nullable|numeric',
            'variations.*.quantity' => 'required|integer',
            'variations.*.discount_type' => 'nullable|string',
            'variations.*.discount_start_date' => 'nullable|date',
            'variations.*.discount_end_date' => 'nullable|date',
            'variations.*.variant_images.*' => 'nullable|array',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'errors'      => $validator->errors()
        ], 422));
    }

    public function messages(): array
    {
        return [
            'variations.required' => 'Variations are required',
            'variations.array' => 'Variations must be an array',
            'variations.*.size.string' => 'Size must be a string',
            'variations.*.color.string' => 'Color must be a string',
            'variations.*.price.required' => 'Price is required',
            'variations.*.price.numeric' => 'Price must be a number',
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
