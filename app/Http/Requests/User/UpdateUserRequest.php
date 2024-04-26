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
            'phone' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'resale_license' => 'nullable|file|mimes:pdf|max:2048',
            'photo_of_id' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'business_name' => 'nullable|string',
            'business_address' => 'nullable|string',
            'online_store_url' => 'nullable|string',
            'ein' => 'nullable|string',
            'about_me' => 'nullable|string',
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
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'phone.required' => 'Phone is required',
            'image.image' => 'Image must be an image',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg',
            'image.max' => 'Image must be less than 2MB',
            'resale_license.file' => 'Resale license must be a file',
            'resale_license.mimes' => 'Resale license must be a file of type: pdf',
            'resale_license.max' => 'Resale license must be less than 2MB',
            'photo_of_id.file' => 'Photo of ID must be a file',
            'photo_of_id.mimes' => 'Photo of ID must be a file of type: jpeg, png, jpg',
            'photo_of_id.max' => 'Photo of ID must be less than 2MB',
            'business_name.string' => 'Business name must be a string',
            'business_address.string' => 'Business address must be a string',
            'online_store_url.string' => 'Online store URL must be a string',
            'ein.string' => 'EIN must be a string',
            'about_me.string' => 'About me must be a string',
        ];
    }
}
