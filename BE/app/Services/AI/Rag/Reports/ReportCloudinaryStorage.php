<?php

namespace App\Services\AI\Rag\Reports;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ReportCloudinaryStorage
{
    /**
     * @param  resource|string  $file
     */
    public function uploadRaw($file, string $folder, string $basename, string $extension): string
    {
        if (function_exists('cloudinary') && $this->cloudinaryUrl() !== '') {
            try {
                return $this->uploadToCloudinary($file, $folder, $basename, $extension);
            } catch (\Throwable $e) {
                Log::warning('ReportCloudinaryStorage: Cloudinary upload failed, using local storage', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $contents = is_string($file) ? file_get_contents($file) : stream_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException('Không đọc được file để lưu.');
        }

        return $this->storeLocally($contents, $folder, $basename, $extension);
    }

    public function uploadRawFromString(string $contents, string $folder, string $basename, string $extension): string
    {
        if (function_exists('cloudinary') && $this->cloudinaryUrl() !== '') {
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

        return $this->storeLocally($contents, $folder, $basename, $extension);
    }

    /**
     * @param  resource|string  $file
     */
    private function uploadToCloudinary($file, string $folder, string $basename, string $extension): string
    {
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

    private function storeLocally(string $contents, string $folder, string $basename, string $extension): string
    {
        $relative = trim($folder, '/') . '/' . $basename . '.' . ltrim($extension, '.');
        Storage::disk('public')->put($relative, $contents);

        return Storage::disk('public')->url($relative);
    }

    private function cloudinaryUrl(): string
    {
        return (string) config('filesystems.disks.cloudinary.url', '');
    }
}
