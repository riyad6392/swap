<?php

namespace App\Http\Requests\PlanSubscription;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeletePlanSubscriptionRequest extends FormRequest
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
            'subscription_id' => 'required|exists:subscriptions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Subscription is required',
            'subscription_id.exists' => 'Subscription does not exist',
        ];
    }

//    public function failedValidation(Validator $validator): array
//    {
//        throw new HttpResponseException(response()->json([
//            'success'   => false,
//            'message'   => 'Validation errors',
//            'errors'      => $validator->errors()
//        ], 422));
//    }
}
