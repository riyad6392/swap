<?php

namespace App\Http\Requests\ProductVariation;

use Illuminate\Foundation\Http\FormRequest;

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
}
