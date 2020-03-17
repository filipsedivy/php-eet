<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use FilipSedivy\EET\Exceptions\SoapClient\CurlException;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use SoapClient as InternalSoapClient;

/**
 * @method OdeslaniTrzby(array $data)
 */
class SoapClient extends InternalSoapClient
{
    /** @var Certificate */
    private $certificate;

    /** @var bool */
    private $trace;

    /** @var float */
    private $connectionStartTime;

    /** @var float */
    private $lastResponseStartTime;

    /** @var float */
    private $lastResponseEndTime;

    /** @var int|null */
    private $lastResponseHttpCode;

    /** @var string */
    public $lastRequest;

    /** @var string|null */
    public $lastResponse;

    /** @var bool */
    private $returnRequest = false;

    /** @var int|null timeout in milliseconds */
    private $timeout = 2500;

    /** @var int|null connection timeout in milliseconds */
    private $connectTimeout = 2000;

    /** @var array */
    private $curlOptions;

    public function __construct(string $service, Certificate $certificate, bool $trace = false, array $curlOptions = [])
    {
        parent::__construct($service, [
            'exceptions' => true,
            'trace' => $trace
        ]);

        $this->certificate = $certificate;
        $this->trace = $trace;
        $this->curlOptions = $curlOptions;
    }

    public function getXML($request)
    {
        $doc = new \DOMDocument('1.0');
        $doc->loadXML($request);

        $objWSSE = new WSSESoap($doc);
        $objWSSE->addTimestamp();

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $objKey->loadKey($this->certificate->getPrivateKey());
        $objWSSE->signSoapDoc($objKey, ['algorithm' => XMLSecurityDSig::SHA256]);

        $token = $objWSSE->addBinaryToken($this->certificate->getCertificate());
        $objWSSE->attachTokentoSig($token);

        return $objWSSE->saveXML();
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0): ?string
    {
        $xml = $this->getXML($request);
        $this->lastRequest = $xml;

        if ($this->returnRequest) {
            return '';
        }

        $this->trace && $this->lastResponseStartTime = microtime(true);

        $this->lastResponse = $this->doRequestByCurl($xml, $location, $action, $version, $one_way);

        $this->trace && $this->lastResponseEndTime = microtime(true);

        return $this->lastResponse;
    }

    public function doRequestByCurl(string $request, string $location, string $action, int $version, int $one_way = 0): ?string
    {
        $this->lastResponseHttpCode = null;

        $curl = curl_init($location);

        if ($curl === false) {
            throw new CurlException('Curl initialisation failed');
        }

        $headers = array(
            'User-Agent: PHP-SOAP',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
            'Content-Length: ' . strlen($request),
        );

        $options = array(
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HEADER => $headers,
            CURLOPT_HTTPHEADER => [
                sprintf('Content-Type: %s', $version === 2 ? 'application/soap+xml' : 'text/xml'),
                sprintf('SOAPAction: %s', $action)
            ],
        );

        $options = array_replace($options, $this->curlOptions);

        $options = $this->curlSetTimeoutOption($options, $this->timeout, 'CURLOPT_TIMEOUT');
        $options = $this->curlSetTimeoutOption($options, $this->connectTimeout, 'CURLOPT_CONNECTTIMEOUT');

        $this->setCurlOptions($curl, $options);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            $errorNumber = curl_errno($curl);
            curl_close($curl);

            throw new CurlException($errorMessage, $errorNumber);
        }

        $this->lastResponseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $header_len = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_len);

        curl_close($curl);

        return $one_way ? null : $body;
    }

    private function setCurlOptions($curl, array $options): void
    {
        foreach ($options as $option => $value) {
            if (curl_setopt($curl, $option, $value) !== false) {
                continue;
            }

            $export = var_export($value, true);

            throw new CurlException(sprintf('Failed setting CURL option %d to %s', $option, $export));
        }
    }

    private function curlSetTimeoutOption($options, $milliseconds, $name)
    {
        if ($milliseconds > 0) {
            if (defined("{$name}_MS")) {
                $options[constant("{$name}_MS")] = $milliseconds;
            } else {
                $seconds = ceil($milliseconds / 1000);
                $options[$name] = $seconds;
            }

            if ($milliseconds <= 1000) {
                $options[CURLOPT_NOSIGNAL] = 1;
            }
        }

        return $options;
    }

    public function getLastResponseTime(): float
    {
        return $this->lastResponseEndTime - $this->lastResponseStartTime;
    }

    public function getLastResponseHttpCode(): ?int
    {
        return $this->lastResponseHttpCode;
    }

    public function getConnectionTime(bool $tillLastRequest = false)
    {
        return $tillLastRequest ? $this->getConnectionTimeTillLastRequest() : $this->getConnectionTimeTillNow();
    }

    private function getConnectionTimeTillLastRequest()
    {
        if (!$this->lastResponseEndTime || !$this->connectionStartTime) {
            return null;
        }

        return $this->lastResponseEndTime - $this->connectionStartTime;
    }

    private function getConnectionTimeTillNow()
    {
        if (!$this->connectionStartTime) {
            return null;
        }

        return microtime(true) - $this->connectionStartTime;
    }

    public function __getLastRequest(): string
    {
        return $this->lastRequest;
    }

    public function __getLastResponse(): string
    {
        return (string)$this->lastResponse;
    }

    public function setTimeout($milliseconds): void
    {
        $this->timeout = $milliseconds;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setConnectTimeout($milliseconds): void
    {
        $this->connectTimeout = $milliseconds;
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }
}
