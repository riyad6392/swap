<?php

namespace App\Http\Requests\PlanSubscription;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePlanSubscriptionRequest extends FormRequest
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
            'plan_id' => 'required|exists:plans,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'business_name' => 'required|string',
            'business_address' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Plan is required',
            'plan_id.exists' => 'Plan does not exist',
            'first_name.required' => 'First name is required',
            'first_name.string' => 'First name must be a string',
            'last_name.required' => 'Last name is required',
            'last_name.string' => 'Last name must be a string',
            'business_name.required' => 'Business name is required',
            'business_name.string' => 'Business name must be a string',
            'business_address.required' => 'Business address is required',
            'business_address.string' => 'Business address must be a string',
        ];
    }
}
