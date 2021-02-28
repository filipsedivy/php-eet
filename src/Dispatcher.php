<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use DateTime;
use FilipSedivy\EET\Enum;
use FilipSedivy\EET\Entity\Response;
use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils\Debugger;
use FilipSedivy\EET\Utils\Format;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class Dispatcher
{
    public const PLAYGROUND_SERVICE = 'playground';

    public const PRODUCTION_SERVICE = 'production';

    protected ?string $pkp = null;

    protected ?string $bkp = null;

    protected ?string $fik = null;

    protected ?DateTime $sentDateTime = null;

    protected ?Receipt $lastReceipt = null;

    /** @var array<Response\Warning> */
    protected array $lastWarnings = [];

    private Certificate $certificate;

    private string $service;

    private SoapClient $soapClient;

    /** @var array<string> */
    private array $curlOptions = [];

    public function __construct(
        Certificate $certificate,
        string $service = self::PRODUCTION_SERVICE
    )
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
    }

    public function setService(string $service): void
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

    /**
     * @return non-empty-array<string, array<string, string>>
     * @throws \Exception
     */
    public function getCheckCodes(Receipt $receipt): array
    {
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

    public function send(Receipt $receipt, bool $check = false): ?string
    {
        $this->initSoapClient();

        try {
            $response = $this->processData($receipt, $check);
        } catch (Exceptions\SoapClient\CurlException $exception) {
            throw new Exceptions\EET\ClientException($receipt, $this->pkp, $this->bkp, $exception);
        }

        if (isset($response->Chyba)) {
            $errorResult = $response->Chyba;
            $errorResponse = new Response\Error($errorResult->kod, $errorResult->test);
            throw Exceptions\EET\ErrorException::fromErrorResponse($errorResponse);
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

    /**
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function prepareData(Receipt $receipt, bool $check = false): array
    {
        $this->sentDateTime = new DateTime;
        $head = $receipt->buildHeader();
        $head += [
            'dat_odesl' => $this->sentDateTime->format('c'),
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

    public function getSentDateTime(): ?DateTime
    {
        return $this->sentDateTime;
    }

    public function getFik(): ?string
    {
        return $this->fik;
    }

    public function getLastReceipt(): ?Receipt
    {
        return $this->lastReceipt;
    }

    /** @return array<Response\Warning> */
    public function getWarnings(): array
    {
        return $this->lastWarnings;
    }

    /** @param mixed $value */
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

    private function processData(Receipt $receipt, bool $check = false): object
    {
        $data = $this->prepareData($receipt, $check);

        return $this->getSoapClient()->OdeslaniTrzby($data);
    }

    private function processWarnings(object $warnings): void
    {
        $this->lastWarnings = [];

        if (is_iterable($warnings)) {
            foreach ($warnings as $warning) {
                $this->lastWarnings[] = new Response\Warning($warning->kod_varov);
            }
        } else {
            $this->lastWarnings[] = new Response\Warning($warnings->kod_varov);
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
}
