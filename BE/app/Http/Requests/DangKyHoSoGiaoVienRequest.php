<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DangKyHoSoGiaoVienRequest extends FormRequest
{
    /**
     * Ai được phép thực hiện request này
     */
    public function authorize(): bool
    {
        return true; // Kiểm soát quyền trong Controller
    }

    /**
     * Các rule validate
     */
    public function rules(): array
    {
        return [
            'ho_ten'         => 'required|string|max:255',
            'so_dien_thoai'  => ['nullable', 'string', 'regex:/^(0[35789][0-9]{8}|\+84[35789][0-9]{8})$/'],
            'chuyen_mon'     => 'required|string|in:Giáo viên,Chuyên gia',
            'mo_ta'          => 'nullable|string|max:2000',
            // Ảnh đại diện (tuỳ chọn)
            'anh_dai_dien'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            // KYC – bắt buộc
            'cccd_mat_truoc' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'cccd_mat_sau'   => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            // Bằng cấp / Chứng chỉ – bắt buộc
            'bang_cap'       => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ];
    }

    /**
     * Thông báo lỗi tuỳ chỉnh (hiển thị trực tiếp trên giao diện)
     */
    public function messages(): array
    {
        return [
            'ho_ten.required'          => 'Họ và tên không được để trống.',
            'ho_ten.max'               => 'Họ và tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.regex'      => 'Số điện thoại không hợp lệ. Vui lòng nhập đúng định dạng (VD: 0901234567).',
            'chuyen_mon.required'      => 'Vui lòng chọn nghề nghiệp.',
            'chuyen_mon.in'            => 'Nghề nghiệp không hợp lệ. Vui lòng chọn Giáo viên hoặc Chuyên gia.',
            'mo_ta.max'                => 'Giới thiệu bản thân không được vượt quá 2000 ký tự.',
            // Ảnh đại diện
            'anh_dai_dien.file'        => 'Ảnh đại diện phải là một file hợp lệ.',
            'anh_dai_dien.mimes'       => 'Ảnh đại diện phải là định dạng JPG, PNG hoặc WebP.',
            'anh_dai_dien.max'         => 'Ảnh đại diện không được vượt quá 5MB.',
            // CCCD mặt trước
            'cccd_mat_truoc.required'  => 'Vui lòng tải lên ảnh CCCD/CMND mặt trước.',
            'cccd_mat_truoc.file'      => 'CCCD mặt trước phải là một file hợp lệ.',
            'cccd_mat_truoc.mimes'     => 'CCCD mặt trước phải là định dạng JPG, PNG hoặc WebP.',
            'cccd_mat_truoc.max'       => 'Ảnh CCCD mặt trước không được vượt quá 5MB.',
            // CCCD mặt sau
            'cccd_mat_sau.required'    => 'Vui lòng tải lên ảnh CCCD/CMND mặt sau.',
            'cccd_mat_sau.file'        => 'CCCD mặt sau phải là một file hợp lệ.',
            'cccd_mat_sau.mimes'       => 'CCCD mặt sau phải là định dạng JPG, PNG hoặc WebP.',
            'cccd_mat_sau.max'         => 'Ảnh CCCD mặt sau không được vượt quá 5MB.',
            // Bằng cấp
            'bang_cap.required'        => 'Vui lòng tải lên tài liệu chứng minh năng lực (Bằng cấp / Chứng chỉ).',
            'bang_cap.file'            => 'Tài liệu bằng cấp phải là một file hợp lệ.',
            'bang_cap.mimes'           => 'Tài liệu bằng cấp phải là định dạng JPG, PNG, WebP hoặc PDF.',
            'bang_cap.max'             => 'File tài liệu bằng cấp không được vượt quá 10MB.',
        ];
    }

    /**
     * Ghi đè xử lý khi validation thất bại → trả về JSON thay vì redirect
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
