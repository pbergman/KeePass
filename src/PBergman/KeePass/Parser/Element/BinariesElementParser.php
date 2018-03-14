<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Encoding\BooleanEncoding;
use PBergman\KeePass\Model\ArrayCollection;
use PBergman\KeePass\Node\Binary;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class BinariesElementHandler
 *
 * embedded handler for a binary element
 *
 *  <Binaries>
 *      <Binary ID="0" Compressed="True">...</Binary>
 *      <Binary ID="1" Compressed="True">...</Binary>
 *  </Binaries>
 *
 * @package PBergman\KeePass\Parser
 */
class BinariesElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementHandlerInterface
{
    /** @var ArrayCollection */
    protected $ctx;
    /** @var XmlPathInterface  */
    protected $xpath;

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'Binaries';
    }

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attributes)
    {
        if ('Binary' === $name) {
            $binary = new Binary();
            $binary->setCompressed(BooleanEncoding::unmarshal($attributes['Compressed']));
            $binary->setId((int)$attributes['ID']);
            $this->ctx[] = $binary;
        }
        return XmlParserState::STATE_FEED;
    }


    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * @param ArrayCollection $collection
     */
    public function setContext(ArrayCollection $collection)
    {
        $this->ctx = $collection;
    }

    /**
     * @inheritdoc
     */
    public function onData($data)
    {
        if (!empty($data) && 'Binary' === $this->xpath->current()) {
            $this->ctx->last()->setData($data);
        }

        return XmlParserState::STATE_FEED;
    }

    /**
     * @inheritdoc
     */
    public function onElementClose($parser, $name, $isClose)
    {
        if ($isClose) {
            $this->ctx = null;
        }
        return XmlParserState::STATE_FEED;
    }
}