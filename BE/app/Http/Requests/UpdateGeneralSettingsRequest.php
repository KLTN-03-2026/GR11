<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo_url' => 'nullable|string',
            'logo_icon' => 'nullable|string|max:100',
            'site_name' => 'required|string|max:255',
            'hotline' => 'nullable|string|max:50',
            'support_email' => 'nullable|email|max:150',
            'facebook_url' => 'nullable|url|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            // Thông báo lỗi cho Tên hệ thống (Bắt buộc)
            'site_name.required' => 'Vui lòng nhập tên hệ thống.',
            'site_name.string'   => 'Tên hệ thống phải là một chuỗi văn bản.',
            'site_name.max'      => 'Tên hệ thống không được vượt quá 255 ký tự.',

            // Thông báo lỗi cho Logo URL
            'logo_url.string' => 'Đường dẫn logo phải là một chuỗi văn bản.',

            // Thông báo lỗi cho Logo Icon
            'logo_icon.string' => 'Logo icon phải là một chuỗi văn bản.',
            'logo_icon.max'    => 'Logo icon không được vượt quá 100 ký tự.',

            // Thông báo lỗi cho Hotline
            'hotline.string' => 'Hotline phải là một chuỗi văn bản.',
            'hotline.max'    => 'Hotline không được vượt quá 50 ký tự.',

            // Thông báo lỗi cho Email hỗ trợ
            'support_email.email' => 'Địa chỉ email hỗ trợ không đúng định dạng.',
            'support_email.max'   => 'Email hỗ trợ không được vượt quá 150 ký tự.',

            // Thông báo lỗi cho Facebook URL
            'facebook_url.url' => 'Đường dẫn Facebook không đúng định dạng URL hợp lệ.',
            'facebook_url.max' => 'Đường dẫn Facebook không được vượt quá 255 ký tự.',
        ];
    }
}
