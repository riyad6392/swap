<?php

namespace App\Http\Requests\User;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class ListUserRequest extends FormRequest
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
            'pagination' => 'nullable|numeric',
            'get_all' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
            'sort' => 'nullable|string|max:255|in:asc,desc',
        ];
    }

    public function messages(): array
    {
        return [
            'pagination.numeric' => 'Pagination must be a number',
            'get_all.boolean' => 'Get all must be a boolean',
            'search.string' => 'Search must be a string',
            'search.max' => 'Search must not exceed 255 characters',
            'sort.string' => 'Sort must be a string',
            'sort.max' => 'Sort must not exceed 255 characters',
            'sort.in' => 'Sort must be either asc or desc'
        ];
    }
}
