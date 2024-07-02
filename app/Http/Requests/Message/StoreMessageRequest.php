<?php

namespace App\Http\Requests\Message;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'message' => 'nullable|string',
            'files' => 'sometimes|array',
            'files.*' => 'file|mimes:jpeg,jpg,webp,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:2048', // 2MB max
            'receiver_id' => 'required|integer|exists:users,id',
//            'swap_id' => 'required|integer|exists:swaps,id',
            'sender_id' => 'required|integer|exists:users,id',
            'conversation_id' => 'nullable|integer|exists:conversations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'The message field is required.',
            'receiver_id.required' => 'The receiver_id field is required.',
            'receiver_id.integer' => 'The receiver_id field must be an integer.',
            'swap_id.required' => 'The swap_id field is required.',
            'swap_id.integer' => 'The swap_id field must be an integer.',
            'sender_id.required' => 'The sender_id field is required.',
            'sender_id.integer' => 'The sender_id field must be an integer.',
            'conversation_id.required' => 'The group_id field is required.',
            'conversation_id.integer' => 'The group_id field must be an integer.',
        ];
    }
}
