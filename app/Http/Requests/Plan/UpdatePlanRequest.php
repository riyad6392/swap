<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdatePlanRequest extends FormRequest
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
            'name'              => 'required|unique:plans,name',
            'description'       => 'required',
            'currency'          => 'required',
            'amount'            => 'required|numeric',
            'interval'          => 'required|in:month,year',
            'interval_duration' => 'required|numeric',
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
            'name.required'              => 'Plan name is required',
            'name.unique'                => 'Plan name already exists',
            'description.required'       => 'Plan description is required',
            'currency.required'          => 'Currency is required',
            'amount.required'            => 'Amount is required',
            'amount.numeric'             => 'Amount must be a number',
            'interval.required'          => 'Interval is required',
            'interval.in'                => 'Interval must be month or year',
            'interval_duration.required' => 'Interval duration is required',
            'interval_duration.numeric'  => 'Interval duration must be a number'
        ];
    }
}
