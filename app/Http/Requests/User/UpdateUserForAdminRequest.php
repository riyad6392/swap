<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ValidationErrorMessageTrait;

class UpdateUserForAdminRequest extends FormRequest
{
    use ValidationErrorMessageTrait;
    public function authorize()
    {
        return true;
    }

    public function rules() : array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'. $this->user,
//            'password' => 'nullable|string|min:8|confirmed',
//            'subscription_is_active' => 'nullable|boolean',
            'is_approved_by_admin' => 'nullable|boolean',
//            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
//            'business_address' => 'nullable|string|max:255',
//            'online_store_url' => 'nullable|url|max:255',
//            'ein' => 'nullable|string|max:50',
//            'resale_license' => 'nullable|string|max:50',
//            'photo_of_id' => 'nullable|string|max:255',
//            'stripe_customer_id' => 'nullable|string|max:255',
//            'is_super_swapper' => 'nullable|boolean',
//            'about_me' => 'nullable|string|max:1000',
        ];
    }
}
