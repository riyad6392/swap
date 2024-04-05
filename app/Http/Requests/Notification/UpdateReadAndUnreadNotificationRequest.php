<?php

namespace App\Http\Requests\Notification;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReadAndUnreadNotificationRequest extends FormRequest
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
            'id' => 'required|array'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'id.required' => 'The id field is required.'
        ];
    }
}
