<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatSystemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => 'nullable|integer|min:1',
            'message' => 'required|string|min:1|max:1000',
            'input_type' => 'nullable|string|in:text,voice_text,suggestion',
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.integer' => 'Session không hợp lệ.',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'message.max' => 'Tin nhắn quá dài, vui lòng rút gọn dưới 1000 ký tự.',
        ];
    }
}
