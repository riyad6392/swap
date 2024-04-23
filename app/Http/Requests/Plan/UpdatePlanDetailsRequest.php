<?php

namespace App\Http\Requests\Plan;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdatePlanDetailsRequest extends FormRequest
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
            'plan_details.*.feature' => 'required|string',
            'plan_details.*.features_count' => 'required|numeric',
            'plan_details.*.value' => 'required|string',
            'delete_feature_id' => 'nullable|array',
        ];
    }
    public function messages(): array
    {
        return [
            'plan_details.required' => 'Plan details are required',
            'plan_details.array' => 'Plan details must be an array',
            'plan_details.*.feature.required' => 'Feature is required',
            'plan_details.*.feature.string' => 'Feature must be a string',
            'plan_details.*.features_count.required' => 'Features count is required',
            'plan_details.*.features_count.numeric' => 'Features count must be a number',
            'plan_details.*.value.required' => 'Value is required',
            'plan_details.*.value.string' => 'Value must be a string',
            'delete_feature_id.array' => 'Delete feature id must be an array',
        ];
    }
}
