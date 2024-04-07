<?php

namespace App\Http\Requests\Swap;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreSwapRequest extends FormRequest
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
            'requested_user_id' => 'required|integer|exists:users,id',
            'exchanged_user_id' => 'required|integer|exists:users,id',
            'status' => 'required|string',
//            'requested_wholesale_amount' => 'required|integer',
//            'exchanged_wholesale_amount' => 'required|integer',
//            'requested_total_commission' => 'required|numeric',
//            'exchanged_total_commission' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'requested_user_id.required' => 'Requested user id is required',
            'exchanged_user_id.required' => 'Exchanged user id is required',
            'status.required' => 'Status is required',
//            'requested_wholesale_amount.required' => 'Requested wholesale amount is required',
//            'exchanged_wholesale_amount.required' => 'Exchanged wholesale amount is required',
//            'requested_total_commission.required' => 'Requested total commission is required',
//            'exchanged_total_commission.required' => 'Exchanged total commission is required',
        ];
    }
}
