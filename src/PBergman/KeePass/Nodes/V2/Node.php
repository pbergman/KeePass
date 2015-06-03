<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

use PBergman\KeePass\Headers\V2\Header;
use PBergman\KeePass\Salsa20;

class Node
{
    protected $dom;
    protected $xpath;
    protected $header;

    function __construct($xml, Header $header)
    {
        $this->dom = new \DOMDocument('1.0'. 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        $this->dom->loadXML($xml);
        $this->xpath = new \DOMXPath($this->dom);
        $this->header = $header;
    }

    public function decrypt()
    {
        $salsa20 = new Salsa20(
            hash('sha256', $this->header[Header::PROTECTED_STREAM_KEY], true),
            "\xe8\x30\x09\x4b\x97\x20\x5d\x2a"
        );

        $elements = $this->xpath->query('//String/Value[@Protected="True"]');

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element->textContent = $salsa20->decrypt(base64_decode($element->textContent));
        }
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
    }

    /**
     * @return \DOMXPath
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * @param \DOMXPath $xpath
     */
    public function setXpath($xpath)
    {
        $this->xpath = $xpath;
    }


}