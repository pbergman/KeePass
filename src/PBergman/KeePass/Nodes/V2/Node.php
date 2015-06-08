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
use PBergman\KeePass\Nodes\V2\Entities\Meta;
use PBergman\KeePass\Nodes\V2\Entities\Times;

class Node
{
    protected $dom;
    protected $xpath;
    protected $header;

    function __construct($xml, Header $header)
    {
        $this->dom = new \DOMDocument('1.0' . 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        $this->dom->loadXML($xml);
        $this->xpath = new \DOMXPath($this->dom);
        $this->header = $header;
        $this->uuidToHex();
    }

    public function getMeta()
    {
        return new Meta(
            $this->xpath->query('/KeePassFile/Meta')->item(0),
            $this->dom
        );
    }

    /**
     * @return \DOMNode
     */
    public function getRoot()
    {
        return $this->xpath->query('/KeePassFile/Root')->item(0);
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
     * @param   string $search
     * @param   null $fieldName
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
            switch ($fieldName) {
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

        $elements = $this->xpath->query(implode('', $query), $this->getRoot());
        $return = null;
        if ($elements->length > 0) {
            foreach ($elements as $element) {
                $return[] = new Entry($element, $this->dom);
            }
        }
        return $return;
    }

    /**
     * will build a hierarchy list from parent en entry nodes
     *
     * @param   \DomNodeList $groups
     * @return  array
     */
    function getList(\DomNodeList $groups = null)
    {
        if (is_null($groups)) {
            $groups = $this->getXpath()->query('/KeePassFile/Root/Group');
        }

        $return = [];
        /** @var \DOMElement $group */
        foreach ($groups as $group) {
            $id = $this->xpath->query('UUID', $group)->item(0)->textContent;
            $return[$id]['name'] = $this->xpath->query('Name', $group)->item(0)->textContent;
            if ($this->xpath->query('Group', $group)->length > 0) {
                $return[$id]['groups'] = $this->getList($this->xpath->query('Group', $group));
            }
            if ($this->xpath->query('Entry', $group)->length > 0) {
                foreach ($this->xpath->query('Entry', $group) as $entry) {
                    $name = $this->xpath->query('String/Key[text()="Title"]/../Value', $entry)->item(0)->textContent;
                    $uid = $this->xpath->query('UUID', $entry)->item(0)->textContent;
                    $return[$id]['entries'][$uid] = $name;

                }
            }
        }
        return $return;
    }
}