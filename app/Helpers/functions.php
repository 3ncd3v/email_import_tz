<?php

function file_force_contents($fullPath, $contents, $flags = 0)
{
    $parts = explode('/', $fullPath);
    array_pop($parts);
    $dir = implode('/', $parts);

    if (!is_dir($dir))
        mkdir($dir, 0777, true);

    file_put_contents($fullPath, $contents, $flags);
}

function generateUniqueFileName(string $originalName): string
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $uniqueId = uniqid($baseName . '_', true);

    return $uniqueId . '.' . $extension;
}
