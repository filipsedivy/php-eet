<?php
/**
 * This file is part of the PHP-EET package.
 *
 * (c) Filip Sedivy <mail@filipsedivy.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 * @author  Filip Sedivy <mail@filipsedivy.cz>
 */

function output()
{
    $fargs = func_get_args();
    $output = '';

    foreach ($fargs as $farg)
    {
        if (is_scalar($farg))
        {
            $output .= $farg;
        }
        elseif (is_array($farg))
        {
            $output .= '<pre>' . print_r($farg, true) . '</pre>';
        }
        else
        {
            $output .= '<pre>' . var_export($farg, true) . '</pre>';
        }
    }

    if (php_sapi_name() === 'cli')
    {
        fwrite(STDOUT, strip_tags($output));
        fwrite(STDOUT, PHP_EOL);
    }
    else
    {
        echo $output, '<br>';
    }
}