<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\SoapClient;

use FilipSedivy\EET\Exceptions\RuntimeException;

class CurlException extends RuntimeException implements SoapClientException
{
}
