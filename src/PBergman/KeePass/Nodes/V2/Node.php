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
    /** @var \DOMDocument  */
    protected $dom;
    /** @var \DOMXPath  */
    protected $xpath;
    /** @var Header  */
    protected $header;

     const ANSI_UPPER_CASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞŸŽŠŒ';
     const ANSI_LOWER_CASE = 'abcdefghijklmnopqrstuvwxyzàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿžšœ';
     const MATCH_ALL = 1;
     const MATCH_LAST_WITH = 2;
     const MATCH_START_WITH = 4;
     const MATCH_CONTAINS = 8;
     const MATCH_CASE_INSENSITIVE = 16;

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
     * @param   string  $search
     * @param   null    $fieldName
     * @param   int     $mode
     * @return array|null|Entities\Entry[]
     */
    public function searchEntry($search, $fieldName = null, $mode = self::MATCH_ALL)
    {
        $text = '{TEXT}';
        $replace = ['{TEXT}' => 'text()'];

        if (self::MATCH_CASE_INSENSITIVE === ($mode & self::MATCH_CASE_INSENSITIVE)) {
            $text = 'translate({TEXT}, "{UPPERCASE}", "{LOWERCASE}")';
            $replace['{LOWERCASE}'] = self::ANSI_LOWER_CASE;
            $replace['{UPPERCASE}'] = self::ANSI_UPPER_CASE;
            $search = strtolower($search);
        }

        $text = str_replace(array_keys($replace), array_values($replace), $text);

        if (self::MATCH_ALL === ($mode & self::MATCH_ALL)) {
            $match = sprintf('%s="%s"', $text, $search);
        }

        if (self::MATCH_LAST_WITH === ($mode & self::MATCH_LAST_WITH)) {
            $match = sprintf('"%s" = substring(%s, string-length(text()) - string-length("%s") +1)', $search, $text, $search);
        }

        if (self::MATCH_START_WITH === ($mode & self::MATCH_START_WITH)) {
            $match = sprintf('starts-with(%s,"%s")', $text, $search);
        }

        if (self::MATCH_CONTAINS === ($mode & self::MATCH_CONTAINS)) {
            $match = sprintf('contains(%s,"%s")', $text, $search);
        }

        $query[] = '//Group/Entry/';

        if (is_null($fieldName)) {
            $query[] = sprintf('*[%s]', $match);
            $query[] = "/..";
            $query[] = "|";
            $query[] = '//Group/Entry/*/';
            $query[] = sprintf('*[%s]', $match);
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
                    $query[] = sprintf('%s[%s]', $fieldName, $match);
                    $query[] = "/../..";
                    break;
                case 'Key':
                case 'Value':
                    $query[] = 'String/';
                    $query[] = sprintf('%s[%s]', $fieldName, $match);
                    $query[] = "/../..";
                    break;
                default:
                    $query[] = sprintf('%s[$s]', $fieldName, $match);
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