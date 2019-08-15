<?php

namespace FilipSedivy\EET\Utils;

class FileSystem
{
    public static function isFileExists($path): bool
    {
        return self::isFile($path) && file_exists($path);
    }

    public static function isFile(string $path): bool
    {
        return is_file($path);
    }
}