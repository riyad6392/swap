<?php

namespace App\Http\Requests\Swap;

use Illuminate\Foundation\Http\FormRequest;

class StoreSwapRequest extends FormRequest
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
            'requested_user_id' => 'required|integer',
            'exchanged_user_id' => 'required|integer',
            'status' => 'required|string',
            'requested_wholesale_amount' => 'required|integer',
            'exchanged_wholesale_amount' => 'required|integer',
            'requested_total_commission' => 'required|numeric',
            'exchanged_total_commission' => 'required|numeric',
        ];
    }
}
