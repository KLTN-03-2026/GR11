<?php

namespace App\Services\AI\Rag\Reports;

use RuntimeException;

class ReportCloudinaryStorage
{
    /**
     * @param  resource|string  $file
     */
    public function uploadRaw($file, string $folder, string $basename, string $extension): string
    {
        if (! function_exists('cloudinary')) {
            throw new RuntimeException('Cloudinary chưa được cấu hình.');
        }

        $uploaded = cloudinary()->uploadApi()->upload($file, [
            'folder' => $folder,
            'resource_type' => 'raw',
            'public_id' => $basename,
            'format' => ltrim($extension, '.'),
        ]);

        $url = (string) ($uploaded['secure_url'] ?? '');
        if ($url === '') {
            throw new RuntimeException('Cloudinary không trả về URL tải file.');
        }

        return $url;
    }

    public function uploadRawFromString(string $contents, string $folder, string $basename, string $extension): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'echokids_report_');
        if ($temp === false) {
            throw new RuntimeException('Không tạo được file tạm.');
        }

        $path = $temp . '.' . ltrim($extension, '.');
        file_put_contents($path, $contents);

        try {
            return $this->uploadRaw($path, $folder, $basename, $extension);
        } finally {
            @unlink($path);
            @unlink($temp);
        }
    }
}
