<?php

namespace App\Http\Requests;

use App\Models\NguoiDung;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusQuanLyTaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('content_block')) {
            $this->merge([
                'content_block' => trim((string) $this->input('content_block')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:nguoi_dungs,id',
            'content_block' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(function () {
                    $id = $this->input('id');
                    if ($id === null || $id === '') {
                        return false;
                    }
                    $u = NguoiDung::find($id);

                    return $u && (int) $u->trang_thai === 0;
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID tài khoản cần đổi trạng thái.',
            'id.exists' => 'Tài khoản cần đổi trạng thái không tồn tại.',
            'content_block.required' => 'Vui lòng nhập lý do khóa tài khoản.',
            'content_block.max' => 'Lý do khóa tối đa 255 ký tự.',
        ];
    }
}
