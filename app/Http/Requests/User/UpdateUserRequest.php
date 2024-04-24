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
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'resale_license' => 'required|file|mimes:pdf|max:2048',
            'photo_of_id' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'business_name' => 'required|string',
            'business_address' => 'required|string',
            'online_store_url' => 'required|string',
            'ein' => 'required|string',
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
            'first_name.string' => 'Last name must be a string',
            'last_name.required' => 'Last name is required',
            'last_name.string' => 'Last name must be a string',
            'phone.required' => 'Phone is required',
            'phone.string' => 'Phone must be a string',
            'image.required' => 'Image is required',
            'image.image' => 'Image must be a image',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg',
            'resale_license.required' => 'Resale license is required',
            'resale_license.file' => 'Resale license must be a file',
            'resale_license.mimes' => 'Resale license must be a file of type: pdf',
            'photo_of_id.required' => 'Photo of ID is required',
            'photo_of_id.file' => 'Photo of ID must be a file',
            'photo_of_id.mimes' => 'Photo of ID must be a file of type: jpeg, png, jpg',
            'business_name.required' => 'Business name is required',
            'business_address.required' => 'Business address is required',
            'online_store_url.required' => 'Online store URL is required',
            'ein.required' => 'EIN is required',
        ];
    }
}
