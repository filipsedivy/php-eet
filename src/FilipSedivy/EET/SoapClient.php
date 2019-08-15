<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use FilipSedivy\EET\Exceptions\ClientException;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * @method OdeslaniTrzby(array $data)
 */
class SoapClient extends \SoapClient
{

    /** @var Certificate */
    private $cert;

    /** @var boolean */
    private $traceRequired;

    /** @var float */
    private $connectionStartTime;

    /** @var float */
    private $lastResponseStartTime;

    /** @var float */
    private $lastResponseEndTime;

    /** @var string */
    private $lastRequest;

    /** @var bool */
    private $returnRequest = false;

    /** @var int|null timeout in milliseconds */
    private $timeout = 2500;

    /** @var int|null connection timeout in milliseconds */
    private $connectTimeout = 2000;

    /** @var array Curl options */
    private $curlOptions = array();


    /**
     *
     * @param string $service
     * @param Certificate $cert
     * @param bool $trace
     * @param array $curlOptions
     */
    public function __construct($service, Certificate $cert, $trace = false, array $curlOptions = array())
    {
        $this->connectionStartTime = microtime(true);
        parent::__construct($service, [
            'exceptions' => true,
            'trace' => $trace
        ]);
        $this->cert = $cert;
        $this->traceRequired = $trace;
        $this->curlOptions = $curlOptions;
    }


    /**
     *
     * @param string $request
     *
     * @return mixed
     */
    public function getXML($request)
    {

        $doc = new \DOMDocument('1.0');
        $doc->loadXML($request);

        $objWSSE = new WSSESoap($doc);
        $objWSSE->addTimestamp();

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $objKey->loadKey($this->cert->getPrivateKey());
        $objWSSE->signSoapDoc($objKey, ["algorithm" => XMLSecurityDSig::SHA256]);

        $token = $objWSSE->addBinaryToken($this->cert->getCert());
        $objWSSE->attachTokentoSig($token);

        return $objWSSE->saveXML();
    }


    /**
     *
     * @param   string $request
     * @param   string $location
     * @param   string $saction
     * @param   int $version
     * @param   null|bool $one_way
     *
     * @return  null|string
     */
    public function __doRequest($request, $location, $saction, $version, $one_way = null)
    {

        $xml = $this->getXML($request);
        $this->lastRequest = $xml;
        if ($this->returnRequest) {
            return '';
        }

        $this->traceRequired && $this->lastResponseStartTime = microtime(true);

        $response = $this->__doRequestByCurl($xml, $location, $saction, $version, $one_way);

        $this->traceRequired && $this->lastResponseEndTime = microtime(true);

        return $response;
    }


    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param bool|null $one_way
     *
     * @return string|null
     * @throws ClientException
     */
    public function __doRequestByCurl($request, $location, $action, $version, $one_way = false)
    {
        // Call via Curl and use the timeout a
        $curl = curl_init($location);
        if ($curl === false) {
            throw new ClientException('Curl initialisation failed');
        }
        /** @var $headers array of headers to be sent with request */
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
            CURLOPT_HTTPHEADER => array(sprintf('Content-Type: %s', $version == 2 ? 'application/soap+xml' : 'text/xml'), sprintf('SOAPAction: %s', $action)),
        );

        $options = $options + $this->curlOptions;

        // Timeout in milliseconds
        $options = $this->__curlSetTimeoutOption($options, $this->timeout, 'CURLOPT_TIMEOUT');
        // ConnectTimeout in milliseconds
        $options = $this->__curlSetTimeoutOption($options, $this->connectTimeout, 'CURLOPT_CONNECTTIMEOUT');

        $this->__setCurlOptions($curl, $options);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            $errorNumber = curl_errno($curl);
            curl_close($curl);

            throw new ClientException($errorMessage, $errorNumber);
        }

        $header_len = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_len);

        curl_close($curl);

        return $one_way ? null : $body;
    }


    /**
     * @param           $curl
     * @param   array $options
     *
     * @throws  ClientException
     */
    private function __setCurlOptions($curl, array $options)
    {
        foreach ($options as $option => $value) {
            if (false !== curl_setopt($curl, $option, $value)) {
                continue;
            }
            throw new ClientException(
                sprintf('Failed setting CURL option %d to %s', $option, var_export($value, true))
            );
        }
    }


    /**
     *
     * @param   array $options
     * @param   int|null $milliseconds
     * @param   string $name
     *
     * @return  mixed
     */
    private function __curlSetTimeoutOption($options, $milliseconds, $name)
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


    /**
     *
     * @return float
     */
    public function __getLastResponseTime()
    {
        return $this->lastResponseEndTime - $this->lastResponseStartTime;
    }


    /**
     *
     * @param $tillLastRequest bool
     *
     * @return float|null
     */
    public function __getConnectionTime($tillLastRequest = false)
    {
        return $tillLastRequest ? $this->getConnectionTimeTillLastRequest() : $this->getConnectionTimeTillNow();
    }


    /**
     *
     * @return float|null
     */
    private function getConnectionTimeTillLastRequest()
    {
        if (!$this->lastResponseEndTime || !$this->connectionStartTime) {
            return null;
        }
        return $this->lastResponseEndTime - $this->connectionStartTime;
    }


    /**
     *
     * @return float|null
     */
    private function getConnectionTimeTillNow()
    {
        if (!$this->connectionStartTime) {
            return null;
        }
        return microtime(true) - $this->connectionStartTime;
    }


    /**
     *
     * @return string
     */
    public function __getLastRequest()
    {
        return $this->lastRequest;
    }


    /**
     *
     * @param int|null $milliseconds timeout in milliseconds
     */
    public function setTimeout($milliseconds)
    {
        $this->timeout = $milliseconds;
    }


    /**
     *
     * @return int|null timeout in milliseconds
     */
    public function getTimeout()
    {
        return $this->timeout;
    }


    /**
     *
     * @param int|null $milliseconds
     */
    public function setConnectTimeout($milliseconds)
    {
        $this->connectTimeout = $milliseconds;
    }


    /**
     *
     * @return int|null
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }
}
