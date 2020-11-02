<?php declare(strict_types=1);

namespace FilipSedivy\EET\Utils;

class Format
{
    /**
     * @var string|int|float $value
     * @return string
     */
    public static function price($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

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
