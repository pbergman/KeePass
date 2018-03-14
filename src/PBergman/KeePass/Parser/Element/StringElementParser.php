<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Model\ArrayCollection;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class StringElementParser
 *
 * embedded handler for a string element
 *
 *  <String>
 *      <Key>Password</Key>
 *      <Value Protected="True" />
 *  </String>
 *
 * @package PBergman\KeePass\Parser
 */
class StringElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementHandlerInterface
{
    /** @var ArrayCollection */
    protected $ctx;
    /** @var XmlPathInterface  */
    protected $xpath;
    /** @var bool  */
    protected $byRef = false;
    /** @var array  */
    protected $refList;

    /**
     * StringElementParser constructor.
     *
     * @param array $list
     */
    public function __construct(array &$list)
    {
        $this->refList = &$list;
    }

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'String';
    }

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attr)
    {
        if ('Value' === $name && isset($attr['Protected']) && 'True' === $attr['Protected']) {
            $this->byRef = true;
        } else {
            $this->byRef = false;
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
        static $key;

        switch ($this->xpath->current()) {
            case 'Key':
                $key = $data;
                break;
            case 'Value':
                if ($this->byRef) {
                    if (false !== $index= array_search($data, $this->refList)) {
                        $data = &$this->refList[$index];
                    } else {
//                        var_dump($this->refList, $data);
//                        die('NOT FOUND');
                        $this->refList[] = &$data;
                    }
                    if (null !== $this->ctx) {
                        $values = $this->ctx->toArray();
                        $values[$key] = &$data;
                        $this->ctx->set($values);
                    }
                } else {
                    if (null !== $this->ctx) {
                        $this->ctx[$key] = $data;
                    }
                }
                break;
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