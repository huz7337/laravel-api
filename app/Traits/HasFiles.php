<?php

namespace App\Traits;

trait HasFiles
{
    public function fileUrl(?string $path): ?string
    {
        if (null === $path) {
            return null;
        }

        return rtrim(env('AWS_URL'), '/')
            . '/'
            . $path;
    }
}
