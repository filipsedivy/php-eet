<?php declare(strict_types=1);

namespace XSuchy09\EET\Exceptions\SoapClient;

use XSuchy09\EET\Exceptions\RuntimeException;

class CurlException extends RuntimeException implements SoapClientException
{
}
