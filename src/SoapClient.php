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
 * @author Filip Sedivy <mail@filipsedivy.cz>
 */

namespace FilipSedivy\EET;

use FilipSedivy\EET\Exceptions\ClientException;
use FilipSedivy\EET\Exceptions\EetException;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Class SoapClient
 * @package FilipSedivy\EET
 */
class SoapClient extends \SoapClient {

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
    private $returnRequest = FALSE;

    /** @var int timeout in milliseconds */
    private $timeout = 2500;

    /** @var int connection timeout in milliseconds */
    private $connectTimeout = 2000;


    /**
     *
     * @param string        $service
     * @param Certificate   $cert
     * @param boolean       $trace
     */
    public function __construct($service, Certificate $cert, $trace = FALSE)
    {
        $this->connectionStartTime = microtime(TRUE);
        parent::__construct($service, [
            'exceptions' => TRUE,
            'trace' => $trace
        ]);
        $this->cert = $cert;
        $this->traceRequired = $trace;
    }


    /**
     *
     * @param string $request
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
     * @param  string  $method
     * @param  string  $data
     * @return string
     */
    public function getXMLforMethod($method, $data)
    {
        $this->returnRequest = TRUE;
        $this->$method($data);
        $this->returnRequest = FALSE;
        return $this->lastRequest;
    }


    /**
     *
     * @param   string      $request
     * @param   string      $location
     * @param   string      $saction
     * @param   int         $version
     * @param   null|string $one_way
     * @return  null|string
     */
    public function __doRequest($request, $location, $saction, $version, $one_way = NULL)
    {

        $xml = $this->getXML($request);
        $this->lastRequest = $xml;
        if ($this->returnRequest) {
            return '';
        }

        $this->traceRequired && $this->lastResponseStartTime = microtime(TRUE);

        $response = $this->__doRequestByCurl($xml, $location, $saction, $version, $one_way);

        $this->traceRequired && $this->lastResponseEndTime = microtime(TRUE);

        return $response;
    }


    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param bool   $one_way
     *
     * @return string|null
     * @throws ClientException
     * @throws EetException
     */
    public function __doRequestByCurl($request, $location, $action, $version, $one_way = FALSE)
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
            CURLOPT_VERBOSE        => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $request,
            CURLOPT_HEADER         => $headers,
            CURLOPT_HTTPHEADER     => array(sprintf('Content-Type: %s', $version == 2 ? 'application/soap+xml' : 'text/xml'), sprintf('SOAPAction: %s', $action)),
            CURLOPT_SSL_VERIFYPEER => false
        );
        // Timeout in milliseconds
        $options = $this->__curlSetTimeoutOption($options, $this->timeout, 'CURLOPT_TIMEOUT');
        // ConnectTimeout in milliseconds
        $options = $this->__curlSetTimeoutOption($options, $this->connectTimeout, 'CURLOPT_CONNECTTIMEOUT');

        $this->__setCurlOptions($curl, $options);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            $errorNumber  = curl_errno($curl);
            curl_close($curl);

            if(preg_match("~Couldn't resolve host~", $errorMessage)){
                throw new EetException($errorMessage);
            }else{
                throw new ClientException($errorMessage, $errorNumber);
            }
        }

        $header_len = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_len);
        $body = substr($response, $header_len);

        curl_close($curl);
        // Return?
        if ($one_way) {
            return null;
        } else {
            return $body;
        }
    }


    /**
     *
     * @param           $curl
     * @param   array   $options
     * @throws  ClientException
     */
    private function __setCurlOptions($curl, array $options)
    {
        foreach ($options as $option => $value)
        {
            if (false !== curl_setopt($curl, $option, $value))
            {
                continue;
            }
            throw new ClientException(
                sprintf('Failed setting CURL option %d (%s) to %s', $option, $this->__getCurlOptionName($option), var_export($value, true))
            );
        }
    }


    /**
     *
     * @param   array     $options
     * @param   int       $milliseconds
     * @param   string    $name
     * @return  mixed
     */
    private function __curlSetTimeoutOption($options, $milliseconds, $name)
    {
        if ($milliseconds > 0)
        {
            if (defined("{$name}_MS")) {
                $options[constant("{$name}_MS")] = $milliseconds;
            } else {
                $seconds        = ceil($milliseconds / 1000);
                $options[$name] = $seconds;
            }
            if ($milliseconds <= 1000)
            {
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
     * @return float
     */
    public function __getConnectionTime($tillLastRequest = FALSE)
    {
        return $tillLastRequest ? $this->getConnectionTimeTillLastRequest() : $this->getConnectionTimeTillNow();
    }


    /**
     *
     * @return float|null
     */
    private function getConnectionTimeTillLastRequest()
    {
        if (!$this->lastResponseEndTime || !$this->connectionStartTime)
        {
            return NULL;
        }
        return $this->lastResponseEndTime - $this->connectionStartTime;
    }


    /**
     *
     * @return mixed|null
     */
    private function getConnectionTimeTillNow()
    {
        if (!$this->connectionStartTime)
        {
            return NULL;
        }
        return microtime(TRUE) - $this->connectionStartTime;
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
