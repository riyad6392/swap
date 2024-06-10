<?php

namespace App\Http\Requests\Message;

use App\Traits\ValidationErrorMessageTrait;
use Illuminate\Foundation\Http\FormRequest;

class MessageListRequest extends FormRequest
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
            'paginate_message_id' =>'nullable|integer',
            'sort' => 'nullable|string|in:oldest,newest',
        ];
    }
}
