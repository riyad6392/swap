<?php

namespace App\Http\Requests\PlanSubscription;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePlanSubscriptionRequest extends FormRequest
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
            'plan_id' => 'required|exists:plans,id',
//            'user_id' => 'required|exists:users,id',
//            'start_date' => 'required|date',
//            'end_date' => 'required|date',
//            'price' => 'required|numeric',
//            'payment_type' => 'required|in:monthly,yearly',
//            'status' => 'required|in:active,inactive',
//            'payment_method' => 'required|in:stripe',
//            'payment_token' => 'required',
        ];
    }

    public function failedValidation(Validator $validator): array
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
            'plan_id.required' => 'Plan is required',
            'plan_id.exists' => 'Plan does not exist',
//            'user_id.required' => 'User is required',
//            'user_id.exists' => 'User does not exist',
//            'start_date.required' => 'Start date is required',
//            'start_date.date' => 'Start date must be a date',
//            'end_date.required' => 'End date is required',
//            'end_date.date' => 'End date must be a date',
//            'price.required' => 'Price is required',
//            'price.numeric' => 'Price must be a number',
//            'payment_type.required' => 'Payment type is required',
//            'payment_type.in' => 'Payment type must be one-time, monthly or yearly',
//            'status.required' => 'Status is required',
//            'status.in' => 'Status must be active or inactive',
//            'payment_method.required' => 'Payment method is required',
//            'payment_method.in' => 'Payment method must be paypal or stripe',
//            'payment_token.required' => 'Payment token is required',
        ];
    }
}
