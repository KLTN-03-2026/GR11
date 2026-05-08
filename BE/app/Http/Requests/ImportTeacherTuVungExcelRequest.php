<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportTeacherTuVungExcelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bai_hocs,id',
            'file' => [
                'required',
                'file',
                'max:10240',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_object($value) || ! method_exists($value, 'getClientOriginalExtension')) {
                        $fail('Tệp tải lên không hợp lệ.');

                        return;
                    }
                    $ext = strtolower((string) $value->getClientOriginalExtension());
                    if (! in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
                        $fail('Chỉ chấp nhận file .xlsx, .xls hoặc .csv.');
                    }
                },
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function messages(): array
    {
        return [
            'id.exists' => 'Bài học không tồn tại.',
            'file.required' => 'Vui lòng chọn file Excel.',
            'file.max' => 'File không được vượt quá 10MB.',
        ];
    }
}
