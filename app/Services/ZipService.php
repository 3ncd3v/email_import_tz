<?php

namespace App\Services;

use ZipArchive;

class ZipService
{
    public function extract(string $filePath, string $extractPath): ?string
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            $zip->extractTo($extractPath);
            $fileName = $zip->getNameIndex(0);
            $zip->close();

            return $extractPath . $fileName;
        }

        return null;
    }
}
