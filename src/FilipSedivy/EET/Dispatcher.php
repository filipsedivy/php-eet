<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use FilipSedivy\EET\Enum;
use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils\Debugger;
use FilipSedivy\EET\Utils\Format;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Dispatcher
{
    public const PLAYGROUND_SERVICE = 'playground',
        PRODUCTION_SERVICE = 'production';

    /** @var Certificate */
    private $certificate;

    /** @var string WSDL path or URL */
    private $service;

    /** @var SoapClient */
    private $soapClient;

    /** @var ValidatorInterface|null */
    private $validator;

    /** @var string|null */
    protected $pkp;

    /** @var string|null */
    protected $bkp;

    /** @var string|null */
    protected $fik;

    /** @var Receipt */
    protected $lastReceipt;

    /** @var array */
    protected $lastWarnings = [];

    /** @var array Curl options */
    private $curlOptions = [];

    public function __construct(Certificate $certificate, string $service = self::PRODUCTION_SERVICE, bool $validate = true)
    {
        $this->checkRequirements();
        $this->certificate = $certificate;

        if ($service === self::PLAYGROUND_SERVICE) {
            $this->setPlaygroundService();
        } elseif ($service === self::PRODUCTION_SERVICE) {
            $this->setProductionService();
        } else {
            $this->setService($service);
        }

        if ($validate) {
            $this->initValidator();
        }
    }

    public function setService($service): void
    {
        $this->service = $service;
    }

    public function setPlaygroundService(): void
    {
        $this->setService(__DIR__ . '/Schema/PlaygroundService.wsdl');
    }

    public function setProductionService(): void
    {
        $this->setService(__DIR__ . '/Schema/ProductionService.wsdl');
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function check(Receipt $receipt): bool
    {
        try {
            $this->send($receipt, true);

            return true;
        } catch (Exceptions\EET\ErrorException $e) {
            return false;
        }
    }

    public function test(Receipt $receipt, bool $hiddenSensitiveData = true): void
    {
        $this->check($receipt);

        $debugger = new Debugger\LastRequest($this->soapClient->lastRequest);
        $debugger->hiddenSensitiveData = $hiddenSensitiveData;
        $debugger->out();
    }

    public function getCheckCodes(Receipt $receipt): array
    {
        if (isset($this->validator)) {
            $violations = $this->validator->validate($receipt);

            if ($violations->count() > 0) {
                throw new Exceptions\Receipt\ConstraintViolationException($violations);
            }
        }

        if (isset($receipt->bkp, $receipt->pkp)) {
            $this->pkp = $receipt->pkp;
            $this->bkp = $receipt->bkp;
        } else {
            $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
            $objKey->loadKey($this->certificate->getPrivateKey());

            $arr = [
                $receipt->dic_popl,
                $receipt->id_provoz,
                $receipt->id_pokl,
                $receipt->porad_cis,
                $receipt->dat_trzby->format('c'),
                $receipt->celk_trzba
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

    public function send(Receipt $receipt, bool $check = false): ?string
    {
        $this->initSoapClient();

        try {
            $response = $this->processData($receipt, $check);
        } catch (Exceptions\SoapClient\CurlException $exception) {
            throw new Exceptions\EET\ClientException($receipt, $this->pkp, $this->bkp, $exception);
        }

        if (isset($response->Chyba)) {
            $this->processError($response->Chyba);
        }

        if (isset($response->Varovani)) {
            $this->processWarnings($response->Varovani);
        }

        $this->fik = $check ? null : $response->Potvrzeni->fik;

        return $this->fik;
    }

    public function getSoapClient(): SoapClient
    {
        if (!isset($this->soapClient)) {
            $this->initSoapClient();
        }

        return $this->soapClient;
    }

    public function prepareData(Receipt $receipt, bool $check = false): array
    {
        $head = $receipt->buildHeader();
        $head += [
            'dat_odesl' => time(),
            'overeni' => $check
        ];

        $this->lastReceipt = $receipt;

        return [
            'Hlavicka' => $head,
            'Data' => $receipt->buildBody(),
            'KontrolniKody' => $this->getCheckCodes($receipt)
        ];
    }

    public function getBkp(): ?string
    {
        return $this->bkp;
    }

    public function getPkp(bool $encoded = true): ?string
    {
        $pkp = $this->pkp;

        if ($pkp === null) {
            return null;
        }

        if ($encoded) {
            $pkp = base64_encode($pkp);
        }

        return $pkp;
    }

    public function getFik(): ?string
    {
        return $this->fik;
    }

    public function getLastReceipt(): ?Receipt
    {
        return $this->lastReceipt;
    }

    public function getWarnings(): array
    {
        return $this->lastWarnings;
    }

    public function setCurlOption(int $option, $value = null): self
    {
        $this->curlOptions[$option] = $value;

        return $this;
    }

    private function checkRequirements(): void
    {
        if (!class_exists(\SoapClient::class)) {
            throw new Exceptions\ExtensionNotFound('php_soap.dll');
        }
    }

    private function processData(Receipt $receipt, bool $check = false)
    {
        $data = $this->prepareData($receipt, $check);

        return $this->getSoapClient()->OdeslaniTrzby($data);
    }

    private function processError($error): void
    {
        if ($error->kod) {
            $msg = Enum\Error::LIST[$error->kod] ?? '';

            throw new Exceptions\EET\ErrorException($msg, $error->kod);
        }
    }

    private function processWarnings($warnings): void
    {
        $this->lastWarnings = [];

        if (is_array($warnings)) {
            foreach ($warnings as $warning) {
                $this->lastWarnings[] = [
                    'code' => $warning->kod_varov,
                    'message' => Enum\Warning::LIST[$warning->kod_varov] ?? ''
                ];
            }
        } else {
            $this->lastWarnings[] = [
                'code' => $warnings->kod_varov,
                'message' => Enum\Warning::LIST[$warnings->kod_varov] ?? ''
            ];
        }
    }

    private function initSoapClient(): void
    {
        if (!isset($this->service)) {
            throw new Exceptions\RuntimeException('Service is not set. Use self::set(Production|Playground)Service()');
        }

        if (!isset($this->soapClient)) {
            $this->soapClient = new SoapClient($this->service, $this->certificate, false, $this->curlOptions);
        }
    }

    private function initValidator(): void
    {
        if (!isset($this->validator)) {
            $this->validator = $this->buildValidatorInterface();
        }
    }

    private function buildValidatorInterface(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
    }
}
