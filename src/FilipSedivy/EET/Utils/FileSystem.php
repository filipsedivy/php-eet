<?php declare(strict_types=1);

namespace FilipSedivy\EET\Utils;

use FilipSedivy\EET\Exceptions;

class FileSystem
{
    public static function isFileExists(string $file): bool
    {
        return self::isFile($file) && file_exists($file);
    }

    public static function isFile(string $file): bool
    {
        return is_file($file);
    }

    public static function isReadable(string $file): bool
    {
        return self::isFile($file) && is_readable($file);
    }

    public static function read(string $file): string
    {
        $content = @file_get_contents($file);

        if ($content === false) {
            throw new Exceptions\IOException("Unable to read file '$file'.");
        }

        return $content;
    }
}