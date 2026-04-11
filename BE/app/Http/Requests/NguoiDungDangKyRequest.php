<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NguoiDungDangKyRequest extends FormRequest
{
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
            'ho_ten'    => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:nguoi_dungs,email',
            'password'  => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'sdt'       => 'nullable|string|max:10',
            'ngay_sinh' => 'nullable|date|before:today',
        ];
    }
    public function messages(): array
    {
        return [
            'ho_ten.required' => 'Vui lòng nhập họ và tên.',
            'ho_ten.string'   => 'Họ và tên phải là chuỗi ký tự.',
            'ho_ten.max'      => 'Họ và tên không được vượt quá 100 ký tự.',
            'email.required'  => 'Vui lòng nhập địa chỉ email.',
            'email.email'     => 'Địa chỉ email không đúng định dạng.',
            'email.max'       => 'Địa chỉ email không được vượt quá 150 ký tự.',
            'email.unique'    => 'Email này đã được sử dụng. Vui lòng chọn email khác!',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.string'   => 'Mật khẩu không hợp lệ.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
            'password.regex'    => 'Mật khẩu phải có ít nhất một chữ thường (a-z), một chữ in hoa (A-Z) và một chữ số.',
            'sdt.string' => 'Số điện thoại không hợp lệ.',
            'sdt.max'    => 'Số điện thoại tối đa 10 chữ số.',

            // Thông báo cho trường ngay_sinh
            'ngay_sinh.date'   => 'Ngày sinh không đúng định dạng ngày tháng.',
            'ngay_sinh.before' => 'Ngày sinh phải là một ngày trong quá khứ.',
        ];
    }
}
