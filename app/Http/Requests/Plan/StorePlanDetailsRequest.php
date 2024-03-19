<?php

namespace App\Http\Requests\Plan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePlanDetailsRequest extends FormRequest
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
            'feature' => 'required',
            'features_count' => 'required|numeric',
            'value' => 'required|string',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()
        ], 422));
    }

    public function messages(): array
    {
        return [
            'feature.required' => 'Feature is required',
            'features_count.required' => 'Features count is required',
            'features_count.numeric' => 'Features count must be a number',
            'value.required' => 'Value is required',
            'value.string' => 'Value must be a string'
        ];
    }
}
