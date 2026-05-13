<?php

namespace App\Http\Concerns;

trait ResolvesTeacherMediaUrl
{
    protected function resolveTeacherMediaUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }
}
