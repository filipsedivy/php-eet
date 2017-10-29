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
use FilipSedivy\EET\Exceptions\RequirementsException;
use FilipSedivy\EET\Exceptions\ServerException;
use FilipSedivy\EET\Utils\Format;
use RobRichards\XMLSecLibs\XMLSecurityKey;


/**
 * Class Dispatcher
 * @package FilipSedivy\EET
 */
class Dispatcher
{

    /** @var Certificate */
    private $cert;

    /** @var string WSDL path or URL */
    private $service;

    /** @var SoapClient */
    private $soapClient;

    /** @var string|bool */
    private $trace = false;

    /** @var string Generated PKP from Receipt */
    protected $pkp;

    /** @var string Generated BKP from Receipt */
    protected $bkp;

    /** @var string Received FIK */
    protected $fik;

    /** @var Receipt Last Receipt */
    protected $lastReceipt;


    /**
     * Initialization
     *
     * @param Certificate $cert
     */
    public function __construct(Certificate $cert)
    {
        $this->cert = $cert;
        $this->checkRequirements();
    }


    /**
     * Setting WSDL path or URL
     *
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


    /**
     * Test environment for testing the functionality of the program
     */
    public function setPlaygroundService()
    {
        $this->setService(__DIR__."/Schema/PlaygroundService.wsdl");
    }


    /**
     * Production environment for sending receipt
     */
    public function setProductionService()
    {
        $this->setService(__DIR__."/Schema/ProductionService.wsdl");
    }


    /**
     * Checking the accuracy of data sent
     *
     * @param Receipt $receipt
     * @return boolean|string
     */
    public function check(Receipt $receipt)
    {
        try
        {
            return $this->send($receipt, TRUE);
        } catch (ServerException $e) {
            return FALSE;
        }
    }



    /**
     * Check codes
     *
     * @param Receipt $receipt
     * @return array
     */
    public function getCheckCodes(Receipt $receipt)
    {
        if(isset($receipt->bkp, $receipt->pkp))
        {
            $this->pkp = $receipt->pkp;
            $this->bkp = $receipt->bkp;
        }
        else
        {
            $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
            $objKey->loadKey($this->cert->getPrivateKey());

            $arr = [
                $receipt->dic_popl,
                $receipt->id_provoz,
                $receipt->id_pokl,
                $receipt->porad_cis,
                $receipt->dat_trzby->format('c'),
                Format::price($receipt->celk_trzba)
            ];

            $this->pkp = $objKey->signData(implode('|', $arr));
            $this->bkp = Format::BKB(sha1($this->pkp));
        }

        return [
            'pkp' => [
                '_' => $this->pkp,
                'digest' => 'SHA256',
                'cipher' => 'RSA2048',
                'encoding' => 'base64'
            ],
            'bkp' => [
                '_' => $this->bkp,
                'digest' => 'SHA1',
                'encoding' => 'base16'
            ]
        ];
    }


    /**
     * Send receipt
     *
     * @param Receipt $receipt
     * @param boolean $check
     * @return boolean|string
     */
    public function send(Receipt $receipt, $check = FALSE)
    {
        $this->initSoapClient();

        $response = $this->processData($receipt, $check);

        isset($response->Chyba) && $this->processError($response->Chyba);

        $this->fik = $check ? TRUE : $response->Potvrzeni->fik;
        return $this->fik;
    }

    /**
     *
     * @throws RequirementsException
     * @return void
     */
    private function checkRequirements()
    {
        if (!class_exists('\SoapClient'))
        {
            throw new RequirementsException('Class SoapClient is not defined! Please, allow php extension php_soap.dll in php.ini');
        }
    }

    /**
     * Get (or if not exists: initialize and get) SOAP client.
     *
     * @return SoapClient
     */
    public function getSoapClient()
    {
        !isset($this->soapClient) && $this->initSoapClient();
        return $this->soapClient;
    }


    /**
     * Require to initialize a new SOAP client for a new request.
     *
     * @return void
     * @throws ClientException
     */
    private function initSoapClient()
    {
        if(!isset($this->service))
        {
            throw new ClientException("Not set service");
        }

        if ($this->soapClient === NULL)
        {
            $this->soapClient = new SoapClient($this->service, $this->cert, $this->trace);
        }
    }


    /**
     * Enable debug mode
     */
    public function enableDebug()
    {
        unset($this->trace);
    }


    /**
     * Get trace from SoapClient
     *
     * @return bool|string
     */
    public function getTrace()
    {
        return $this->trace;
    }


    /**
     * @param  Receipt   $receipt
     * @param  bool      $check
     * @return array
     */
    public function prepareData($receipt, $check = FALSE)
    {
        $head = [
            'uuid_zpravy' => $receipt->uuid_zpravy,
            'dat_odesl' => time(),
            'prvni_zaslani' => $receipt->prvni_zaslani,
            'overeni' => $check
        ];

        $body = [
            'dic_popl' => $receipt->dic_popl,
            'dic_poverujiciho' => $receipt->dic_poverujiciho,
            'id_provoz' => $receipt->id_provoz,
            'id_pokl' => $receipt->id_pokl,
            'porad_cis' => $receipt->porad_cis,
            'dat_trzby' => $receipt->dat_trzby->format('c'),
            'celk_trzba' => Format::price($receipt->celk_trzba),
            'zakl_nepodl_dph' => Format::price($receipt->zakl_nepodl_dph),
            'zakl_dan1' => Format::price($receipt->zakl_dan1),
            'dan1' => Format::price($receipt->dan1),
            'zakl_dan2' => Format::price($receipt->zakl_dan2),
            'dan2' => Format::price($receipt->dan2),
            'zakl_dan3' => Format::price($receipt->zakl_dan3),
            'dan3' => Format::price($receipt->dan3),
            'cest_sluz' => Format::price($receipt->cest_sluz),
            'pouzit_zboz1' => Format::price($receipt->pouzit_zboz1),
            'pouzit_zboz2' => Format::price($receipt->pouzit_zboz2),
            'pouzit_zboz3' => Format::price($receipt->pouzit_zboz3),
            'urceno_cerp_zuct' => Format::price($receipt->urceno_cerp_zuct),
            'cerp_zuct' => Format::price($receipt->cerp_zuct),
            'rezim' => $receipt->rezim
        ];

        $this->lastReceipt = $receipt;

        return [
            'Hlavicka' => $head,
            'Data' => $body,
            'KontrolniKody' => $this->getCheckCodes($receipt)
        ];
    }


    /**
     *
     * @param   Receipt     $receipt
     * @param   boolean     $check
     * @return  object
     */
    private function processData(Receipt $receipt, $check = FALSE)
    {
        $data = $this->prepareData($receipt, $check);

        return $this->getSoapClient()->OdeslaniTrzby($data);
    }


    /**
     * @return string
     */
    public function getBkp()
    {
        return $this->bkp;
    }


    /**
     * @param   bool $encoded
     * @return  string
     */
    public function getPkp($encoded = true)
    {
        if($encoded){
            return base64_encode($this->pkp);
        }
        return $this->pkp;
    }


    /**
     * @return string
     */
    public function getFik()
    {
        return $this->fik;
    }


    /**
     * @return Receipt
     */
    public function getLastReceipt()
    {
        return $this->lastReceipt;
    }


    /**
     * @param $error
     * @throws ServerException
     */
    private function processError($error)
    {
        if ($error->kod)
        {
            $msgs = [
                -1 => 'Docasna technicka chyba zpracovani – odeslete prosim datovou zpravu pozdeji',
                2 => 'Kodovani XML neni platne',
                3 => 'XML zprava nevyhovela kontrole XML schematu',
                4 => 'Neplatny podpis SOAP zpravy',
                5 => 'Neplatny kontrolni bezpecnostni kod poplatnika (BKP)',
                6 => 'DIC poplatnika ma chybnou strukturu',
                7 => 'Datova zprava je prilis velka',
                8 => 'Datova zprava nebyla zpracovana kvuli technicke chybe nebo chybe dat',
            ];
            $msg = isset($msgs[$error->kod]) ? $msgs[$error->kod] : '';
            throw new ServerException($msg, $error->kod);
        }
    }


}
