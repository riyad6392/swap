<?php

namespace App\Http\Requests\paymentMethod;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePaymentMethodRequest extends FormRequest
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
            'stripe_payment_method_id' => 'required',
            'method_name' => 'required|string',
            'master_key' => 'nullable|string',
            'master_value' => 'nullable|string',
            'name' => 'string',
            'email' => 'email',
            'phone' => 'string',
            'payment_type' => 'string',
            'card_brand' => 'required|string',
            'card_display_brand' => 'required|string',
            'card_last_four' => 'string',
            'card_exp_month' => 'string',
            'card_exp_year' => 'string',
            'card_country' => 'required|string',
            'card_funding' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'email.email' => 'Email must be a valid email',
            'phone.string' => 'Phone must be a string',
            'method_name.required' => 'Method name is required',
            'method_name.string' => 'Method name must be a string',
            'master_key.string' => 'Master key must be a string',
            'master_value.string' => 'Master value must be a string',
            'stripe_payment_method_id.required' => 'Stripe payment method id is required',
        ];
    }
}
