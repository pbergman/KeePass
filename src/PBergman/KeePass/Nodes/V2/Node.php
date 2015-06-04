<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

use PBergman\KeePass\Crypt\Salsa20\Salsa20Cipher;
use PBergman\KeePass\Headers\V2\Header;
use PBergman\KeePass\KeePass;
use PBergman\KeePass\Nodes\V2\Entities\Entry;
use PBergman\KeePass\Nodes\V2\Entities\Times;

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
        $this->uuidToHex();
    }

    /**
     * Set all ids(UUID) back to hex
     */
    protected function uuidToHex()
    {
        $elements = $this->xpath->query('//UUID');
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element->textContent = strtoupper(bin2hex(base64_decode($element->textContent)));
        }
    }

    /**
     * Decrypt encrypt elements in tree
     */
    public function decrypt()
    {
        $key = hash('sha256', $this->header[Header::PROTECTED_STREAM_KEY], true);
        $salsa20 = new Salsa20Cipher($key, KeePass::STREAM_IV);
        $elements = $this->xpath->query('//String/Value[@Protected="True"]');
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element->textContent = $salsa20->decrypt(base64_decode($element->textContent));
        }
    }

    /**
     * extend xpath with php functions
     */
    public function extendXpath()
    {
        // Register the php: namespace (required)
        $this->xpath->registerNamespace("php", "http://php.net/xpath");
        // Register PHP functions (no restrictions)
        $this->xpath->registerPHPFunctions();
    }

    /**
     * @return \DOMXPath
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * @return Header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return \DOMDocument
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * @param   string  $search
     * @param   null    $fieldName
     * @return  null|array|Entry[]
     */
    public function searchEntry($search, $fieldName = null)
    {
        $query[] = '//Group/Entry/';

        if (is_null($fieldName)) {
            $query[] = sprintf('*[text()="%s"]', $search);
            $query[] = "/..";
            $query[] = "|";
            $query[] = '//Group/Entry/*/';
            $query[] = sprintf('*[text()="%s"]', $search);
            $query[] = "/../..";
        } else {
            switch($fieldName) {
                case 'CreationTime':
                case 'LastModificationTime':
                case 'LastAccessTime':
                case 'ExpiryTime':
                case 'UsageCount':
                case 'LocationChanged':
                    if ($search instanceof \DateTime) {
                        $search = $search
                            ->setTimezone(new \DateTimeZone("Z"))
                            ->format(Times::DATE_FORMAT);
                    }
                    $query[] = 'Times/';
                    $query[] = sprintf('%s[text()="%s"]', $fieldName, $search);
                    $query[] = "/../..";
                    break;
                case 'Key':
                case 'Value':
                    $query[] = 'String/';
                    $query[] = sprintf('%s[text()="%s"]', $fieldName, $search);
                    $query[] = "/../..";
                    break;
                default:
                    $query[] = sprintf('%s[text()="%s"]', $fieldName, $search);
                    $query[] = "/..";
                    break;
            }
        }

        $elements = $this->xpath->query(implode('', $query));
        $return = null;
        if ($elements->length > 0) {
            foreach($elements as $element) {
                $return[] = new Entry($element, $this->dom);
            }
        }
        return $return;
    }
}