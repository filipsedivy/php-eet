<?php declare(strict_types=1);

namespace FilipSedivy\EET\Utils;

class Format
{
    public static function BKB(string $code): string
    {
        $r = '';

        for ($i = 0; $i < 40; $i++) {
            if ($i % 8 === 0 && $i !== 0) {
                $r .= '-';
            }

            $r .= $code[$i];
        }

        return $r;
    }
}
