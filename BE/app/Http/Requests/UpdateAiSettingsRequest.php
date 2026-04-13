<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAiSettingsRequest extends FormRequest
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
            'speech_to_text' => 'required|array',
            'speech_to_text.is_active' => 'required|boolean',
            'speech_to_text.api_key' => 'nullable|string|max:255',
            'speech_to_text.monthly_limit' => 'required|integer|min:0',
            'speech_to_text.current_usage' => 'nullable|integer|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            // Thông báo lỗi cho mảng cha
            'speech_to_text.required' => 'Vui lòng cung cấp dữ liệu cấu hình cho dịch vụ Speech-to-Text.',
            'speech_to_text.array'    => 'Dữ liệu cấu hình phải có định dạng mảng (array).',

            // Thông báo lỗi cho trạng thái bật/tắt
            'speech_to_text.is_active.required' => 'Vui lòng xác định trạng thái hoạt động (Bật/Tắt) của AI.',
            'speech_to_text.is_active.boolean'  => 'Trạng thái hoạt động không hợp lệ (phải là true hoặc false).',

            // Thông báo lỗi cho API Key
            'speech_to_text.api_key.string' => 'API Key phải là một chuỗi văn bản.',
            'speech_to_text.api_key.max'    => 'API Key không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho Giới hạn theo tháng
            'speech_to_text.monthly_limit.required' => 'Vui lòng thiết lập giới hạn số lượt request trong tháng.',
            'speech_to_text.monthly_limit.integer'  => 'Giới hạn request phải là một số nguyên.',
            'speech_to_text.monthly_limit.min'      => 'Giới hạn request không được nhỏ hơn 0.',

            // Thông báo lỗi cho Số lượt đã sử dụng
            'speech_to_text.current_usage.integer' => 'Số lượt đã sử dụng phải là một số nguyên.',
            'speech_to_text.current_usage.min'     => 'Số lượt đã sử dụng không được nhỏ hơn 0.',
        ];
    }
}
