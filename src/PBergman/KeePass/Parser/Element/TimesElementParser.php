<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Encoding\BooleanEncoding;
use PBergman\KeePass\Encoding\DateTimeEncoding;
use PBergman\KeePass\Model\ArrayCollection;
use PBergman\KeePass\Node\Binary;
use PBergman\KeePass\Node\Times;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class TimesElementParser
 *
 * embedded handler for a times element
 *
 *  <Times>
 *      <CreationTime>2013-02-12T13:41:56Z</CreationTime>
 *      <LastModificationTime>2017-05-15T12:03:39Z</LastModificationTime>
 *      <LastAccessTime>2018-02-14T15:44:55Z</LastAccessTime>
 *      <ExpiryTime>2013-04-17T22:00:00Z</ExpiryTime>
 *      <Expires>False</Expires>
 *      <UsageCount>4196</UsageCount>
 *      <LocationChanged>2013-02-12T13:41:56Z</LocationChanged>
 *  </Times>
 *
 * @package PBergman\KeePass\Parser
 */
class TimesElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementHandlerInterface
{
    /** @var Times */
    protected $ctx;
    /** @var XmlPathInterface  */
    protected $xpath;

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'Times';
    }

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attributes)
    {
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
     * @param Times $times
     */
    public function setContext(Times $times)
    {
        $this->ctx = $times;
    }

    /**
     * @inheritdoc
     */
    public function onData($data)
    {
        $element = $this->xpath->current();

        switch ($element) {
            case 'Expires':
                $data = BooleanEncoding::unmarshal($data);
                break;
            case 'UsageCount':
                $data = (int)$data;
                break;
            default:
                $data = DateTimeEncoding::unmarshal($data);
        }

        $method = 'set' . $element;

        if (!empty($data) && method_exists($this->ctx, $method)) {
            $this->ctx->{$method}($data);
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