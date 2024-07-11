<?php

namespace App\Http\Requests\Swap;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSwapRequest extends FormRequest
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
            'requested_user_id' => 'required|required|integer|exists:users,id',
            'exchanged_user_id' => 'required|required|integer|exists:users,id',
//            'status' => 'sometimes|required|string',

//            'requested_wholesale_amount' => 'sometimes|required|integer',
//            'exchanged_wholesale_amount' => 'sometimes|required|integer',
//            'requested_total_commission' => 'sometimes|required|numeric',
//            'exchanged_total_commission' => 'sometimes|required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'define_type.required' => 'Define type is required',
            'define_type.string' => 'Define type must be a string',
            'define_type.in' => 'Define type must be either request_product or exchange_product',
            'requested_user_id.required' => 'Requested user id is required',
            'requested_user_id.exists' => 'Requested user id does not exist',
            'requested_user_id.integer' => 'Requested user id must be a number',
            'exchanged_user_id.exists' => 'Exchanged user id does not exist',
            'exchanged_user_id.required' => 'Exchanged user id is required',
            'exchanged_user_id.integer' => 'Exchanged user id must be a number',
            'status.required' => 'Status is required',
            'status.string' => 'Status must be a string',
//            'requested_wholesale_amount.required' => 'Requested wholesale amount is required',
//            'requested_wholesale_amount.integer' => 'Requested wholesale amount must be a number',
//            'exchanged_wholesale_amount.required' => 'Exchanged wholesale amount is required',
//            'exchanged_wholesale_amount.integer' => 'Exchanged wholesale amount must be a number',
//            'requested_total_commission.required' => 'Requested total commission is required',
//            'requested_total_commission.numeric' => 'Requested total commission must be a number',
//            'exchanged_total_commission.required' => 'Exchanged total commission is required',
//            'exchanged_total_commission.numeric' => 'Exchanged total commission must be a number',
        ];
    }
}
