<?php

namespace App\Http\Requests\User;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
//            'email' => 'required|email',
            'phone' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'resale_license' => 'required|file|mimes:pdf|max:2048',
            'photo_of_id' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'business_name' => 'required|string',
            'business_address' => 'required|string',
            'online_store_url' => 'required|string',
            'ein' => 'required|string',
        ];
    }
}
