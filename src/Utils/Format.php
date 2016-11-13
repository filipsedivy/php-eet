<?php

/*
 * Copyright (C) 2016 Filip Šedivý
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace FilipSedivy\EET\Utils;

class Format
{
    public static function price($value)
    {
        return number_format($value, 2, '.', '');
    }

    public static function BKB($code)
    {
        $r = '';
        for ($i = 0; $i < 40; $i++) {
            if ($i % 8 == 0 && $i != 0) {
                $r .= '-';
            }
            $r .= $code[$i];
        }
        return $r;
    }
}