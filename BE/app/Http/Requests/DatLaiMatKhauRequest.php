<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatLaiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:nguoi_dungs,email',
            'otp'   => 'required|digits:6',
            'new_password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.exists'   => 'Email không tồn tại trong hệ thống.',
            'otp.required'   => 'Vui lòng nhập mã xác nhận.',
            'otp.digits'     => 'Mã xác nhận phải đủ 6 chữ số.',
            'new_password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'       => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.',
            'new_password.regex'     => 'Mật khẩu phải có ít nhất một chữ thường (a-z), một chữ in hoa (A-Z) và một chữ số.',
        ];
    }
}
