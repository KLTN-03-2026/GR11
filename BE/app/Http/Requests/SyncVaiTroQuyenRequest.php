<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncVaiTroQuyenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vai_tro_id' => 'required|integer|exists:vai_tros,id',
            'quyen_ids' => 'nullable|array',
            'quyen_ids.*' => 'integer|exists:quyens,id',
        ];
    }

    public function messages(): array
    {
        return [
            'vai_tro_id.required' => 'Vui lòng chọn vai trò cần đồng bộ quyền.',
            'vai_tro_id.exists' => 'Vai trò không tồn tại.',
            'quyen_ids.array' => 'Danh sách quyền không hợp lệ.',
            'quyen_ids.*.exists' => 'Có quyền không tồn tại trong hệ thống.',
        ];
    }
}
