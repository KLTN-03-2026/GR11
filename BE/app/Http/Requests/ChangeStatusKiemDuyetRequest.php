<?php

namespace App\Http\Requests;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusKiemDuyetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'trang_thai' => 'nullable',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $id = (int) $this->input('id');
            $isKiemDuyetBaiHoc = $this->is('api/admin/kiem-duyet-bai-hoc/*');

            if ($isKiemDuyetBaiHoc) {
                if (!BaiHoc::where('id', $id)->exists()) {
                    $validator->errors()->add('id', 'Bài học không tồn tại.');
                }
                return;
            }

            if (!DanhMucBaiHoc::where('id', $id)->exists()) {
                $validator->errors()->add('id', 'Danh mục không tồn tại.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID cần đổi trạng thái.',
            'id.integer' => 'ID không hợp lệ.',
        ];
    }
}
