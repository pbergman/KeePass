<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Model\XmIndexElement;
use PBergman\KeePass\Model\XmlIndex;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlElementRootInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class IndexParser
 *
 * @package PBergman\KeePass\Parser\Element
 */
class IndexParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementRootInterface, XmlElementHandlerInterface
{
    /** @var XmlIndex  */
    protected $index;
    /** @var XmIndexElement[]  */
    protected $buf;
    /** @var bool */
    protected $isTitleValue;
    /** @var bool  */
    protected $isInHistory = false;
    /** @var XmlPathInterface  */
    protected $xpath;

    /**
     * IndexParser constructor.
     *
     * @param XmlIndex $index
     */
    public function __construct(XmlIndex $index)
    {
        $this->index = $index;
    }

    /**
     * @inheritdoc
     */
    public function onElementClose($parser, $name, $isClose)
    {
        switch ($name) {
            case 'History':
                $this->isInHistory = false;
                break;
            case 'Entry':
            case 'Group':
                $index = $this->xpath->depth();
                if (isset($this->buf[$index])) {
                    $this->buf[$index]->setOffsetEnd(xml_get_current_byte_index($parser));
                    unset($this->buf[$index]);
                }
                break;
            case 'Meta':
                $this->index->setMetaOffsetEnd(xml_get_current_byte_index($parser));
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attributes)
    {

        switch ($name) {
            case 'History':
                $this->isInHistory = true;
                break;
            case 'Entry':
                $element = new XmIndexElement();
                $element->setOffsetStart(xml_get_current_byte_index($parser) - 6); // <Entry
                $element->setIsHistory($this->isInHistory);
                $this->buf[$this->xpath->depth()] = $element;
                $this->index->addEntry($element);
                break;
            case 'Group':
                $element = new XmIndexElement();
                $element->setOffsetStart(xml_get_current_byte_index($parser) - 6); // <Group
                $element->setIsHistory($this->isInHistory);
                $this->buf[$this->xpath->depth()] = $element;
                $this->index->addGroup($element);
                break;
            case 'Meta':
                $this->index->setMetaOffsetStart(xml_get_current_byte_index($parser) - 5); // <Meta
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'KeePassFile';
    }

    /**
     * @return XmlIndex
     */
    public function getIndexer()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return XmlParserState::STATE_FEED;
    }

    /**
     * @inheritdoc
     */
    public function initChild(XmlElementParserInterface $child)
    {
    }

    /**
     * @inheritdoc
     */
    public function onData($data)
    {
        if (empty($data)) {
            return;
        }

        $element = $this->xpath->current();
        $index = $this->xpath->depth()-1;

        switch ($element) {
            case 'Value':
                if ($this->isTitleValue) {
                    $this->buf[$index-1]->setName($data);
                }
                break;
            case 'Name':
                if (isset($this->buf[$index])) {
                    $this->buf[$index]->setName($data);
                }
                break;
            case 'UUID':
                if (isset($this->buf[$index])) {
                    $this->buf[$index]->setId(bin2hex(base64_decode($data)));
                }
                break;
        }

        $this->isTitleValue = ('Key' === $element && 'Title' === $data);
    }
}