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
//            'number' => 'required|numeric|digits_between:13,19',
//            'exp_month' => 'required',
//            'exp_year' => 'required',
//            'cvc' => 'required',
            'name' => 'string',
            'email' => 'email',
            'phone' => 'string',

        ];
    }
    public function messages(): array
    {
        return [
//            'number.required' => 'Card number is required',
//            'number.numeric' => 'Card number must be numeric',
//            'number.digits_between' => 'Card number must be between 13 and 19 digits',
//            'exp_month.required' => 'Expiry month is required',
//            'exp_year.required' => 'Expiry year is required',
//            'cvc.required' => 'CVC is required',
            'name.string' => 'Name must be a string',
            'email.email' => 'Email must be a valid email',
            'phone.string' => 'Phone must be a string',
        ];
    }
}
