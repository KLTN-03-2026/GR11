<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlertSettingsRequest extends FormRequest
{
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
            'is_active' => 'required|boolean',
            'message' => 'nullable|string|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            // Thông báo lỗi cho trạng thái
            'is_active.required' => 'Vui lòng xác định trạng thái hiển thị (Bật/Tắt) của cảnh báo hệ thống.',
            'is_active.boolean'  => 'Trạng thái hiển thị không hợp lệ (phải là định dạng đúng/sai).',

            // Thông báo lỗi cho nội dung tin nhắn
            'message.string'     => 'Nội dung thông báo phải là một chuỗi văn bản.',
            'message.max'        => 'Nội dung thông báo không được dài quá 1000 ký tự.',
        ];
    }
}
