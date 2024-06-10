<?php

namespace App\Http\Requests\Admin;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'role_id' => 'required|string|exists:roles,id',
            'phone' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'profile_image.required' => 'Image is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'phone.required' =>  'phone is required',
            'role.required' => 'Role is required',
            'role.exists' => 'Role does not exist'
        ];
    }
}
