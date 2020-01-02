<?php declare(strict_types=1);

namespace FilipSedivy\EET\Utils\Debugger;

use DOMDocument;

class LastRequest
{
    private const SENSITIVE_TAGS = [
        'DigestValue',
        'SignatureValue',
        'BinarySecurityToken'
    ];

    /** @var bool */
    public $highlight = true;

    /** @var bool */
    public $format = true;

    /** @var bool */
    public $hiddenSensitiveData = true;

    /** @var string */
    public $sensitiveValue = '**** CENSURE ****';

    /** @var string */
    private $lastRequest;

    public function __construct(string $lastRequest)
    {
        $this->lastRequest = $lastRequest;
    }

    public function out(): void
    {
        if ($this->hiddenSensitiveData) {
            $this->doHiddenSensitiveData();
        }

        if ($this->format) {
            $this->doFormat();
        }

        if ($this->highlight) {
            $this->doHighlight();
        }

        printf('<pre>%s</pre>', $this->lastRequest);
    }

    private function doFormat(): void
    {
        $dom = new DOMDocument;

        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($this->lastRequest);

        $this->lastRequest = $dom->saveXML();
    }

    private function doHighlight(): void
    {
        $s = htmlspecialchars($this->lastRequest);

        $s = preg_replace("#&lt;([/]*?)(.*)([\s]*?)&gt;#sU",
            "<span style=\"color: #0000FF\">&lt;\\1\\2\\3&gt;</span>", $s);

        $s = preg_replace("#&lt;([\?])(.*)([\?])&gt;#sU",
            "<span style=\"color: #800000\">&lt;\\1\\2\\3&gt;</span>", $s);

        $s = preg_replace("#&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)#iU",
            "&lt;<span style=\"color: #808000\">\\1\\2</span>\\3", $s);

        $s = preg_replace("#&lt;([/])([^\s]*?)([\s\]]*?)&gt;#iU",
            "&lt;\\1<span style=\"color: #808000\">\\2</span>\\3&gt;", $s);

        $s = preg_replace("#([^\s]*?)\=(&quot;|')(.*)(&quot;|')#isU",
            "<span style=\"color: #800080\">\\1</span>=<span style=\"color: #FF00FF\">\\2\\3\\4</span>", $s);

        $s = preg_replace("#&lt;(.*)(\[)(.*)(\])&gt;#isU",
            "&lt;\\1<span style=\"color: #800080\">\\2\\3\\4</span>&gt;", $s);

        $this->lastRequest = nl2br($s);
    }

    private function doHiddenSensitiveData(): void
    {
        $dom = new DOMDocument;
        $dom->loadXML($this->lastRequest);

        foreach (self::SENSITIVE_TAGS as $tag) {
            $nodeList = $dom->getElementsByTagName($tag);

            for ($i = 0; $i < $nodeList->length; $i++) {
                $node = $nodeList->item($i);
                $node->nodeValue = $this->sensitiveValue;
            }
        }

        $this->lastRequest = $dom->saveXML();
    }
}