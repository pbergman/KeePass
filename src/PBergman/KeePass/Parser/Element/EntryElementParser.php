<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Encoding\BooleanEncoding;
use PBergman\KeePass\Model\BinaryString;
use PBergman\KeePass\Node\Entry;
use PBergman\KeePass\Node\Group;
use PBergman\KeePass\Node\Times;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlElementRootInterface;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

class EntryElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementRootInterface, XmlElementHandlerInterface
{
    /** @var XmlPathInterface */
    protected $xpath;
    /** @var Entry */
    protected $ctx;

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'Entry';
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
    public function onData($data)
    {
        $element = $this->xpath->current();
        switch ($element) {
            case 'UUID':
                $data = BinaryString::fromBase64($data);
                break;
            case 'IconID':
                $data = (int)$data;
                break;
            case 'Times':
            case 'String':
            case 'History':
                return XmlParserState::STATE_FORWARD_CHILD;
            break;
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
    public function getState()
    {
        return XmlParserState::STATE_FEED;
    }

    /**
     * @inheritdoc
     */
    public function initChild(XmlElementParserInterface $child)
    {
        switch (true) {
            case $child instanceof TimesElementParser:
                if (null === $times = $this->ctx->getTimes()) {
                    $times = new Times();
                    $this->ctx->setTimes($times);
                }
                $child->setContext($times);
                break;
            case $child instanceof StringElementParser:
                $child->setContext($this->ctx->getValues());
                break;
        }
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

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attributes)
    {

        if ($name === 'String' || $name === 'Times') {
            return XmlParserState::STATE_FORWARD_CHILD;
        }

        return XmlParserState::STATE_FEED;
    }

    /**
     * @param Entry $ctx
     */
    public function setCtx(Entry $ctx)
    {
        $this->ctx = $ctx;
    }
}
