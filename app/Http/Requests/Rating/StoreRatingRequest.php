<?php

namespace App\Http\Requests\Rating;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'rated_id' => 'required|exists:users,id',
            'rating' => 'required|numeric|between:0.5,5',
            'comments' => 'nullable|string',
        ];
    }
    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required',
            'user_id.exists' => 'User does not exist',
            'rated_id.required' => 'Rated user is required',
            'rated_id.exists' => 'Rated user does not exist',
            'rating.required' => 'Rating is required',
            'rating.numeric' => 'Rating must be a number',
            'rating.between' => 'Rating must be between 0.5 and 5',
            'comments.string' => 'Comments must be a string'
        ];
    }
}
