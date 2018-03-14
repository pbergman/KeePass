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

class GroupElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementRootInterface, XmlElementHandlerInterface
{
    /** @var XmlPathInterface */
    protected $xpath;
    /** @var int */
    protected $currentDepth;
    /** @var int|null */
    protected $maxDepth;
    /** @var Group[] */
    protected $ctx;

    /**
     * GroupElementParser constructor.
     *
     * @param Group $group
     * @param int $maxDepth
     */
    public function __construct(Group $group, $maxDepth = null)
    {
        $this->ctx[1] = $group;
        $this->maxDepth = $maxDepth;
    }

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'Group';
    }

    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * @return bool
     */
    protected function isValidDepth()
    {
        return is_null($this->maxDepth) || ($this->maxDepth >= $this->currentDepth);
    }

    /**
     * @inheritdoc
     */
    public function onData($data)
    {

        if (!$this->isValidDepth()) {
            return XmlParserState::STATE_FEED;
        }

        $element = $this->xpath->current();

        switch ($element) {
            case 'UUID':
            case 'LastTopVisibleEntry':
                $data = BinaryString::fromBase64($data);
                break;
            case 'IsExpanded':
                $data = BooleanEncoding::unmarshal($data);
                break;
            case 'EnableAutoType':
            case 'EnableSearching':
                if ('null' === $data) { // null string value, inherit form parent
                    $data = null;
                } else {
                    $data = BooleanEncoding::unmarshal($data);
                }
                break;
            case 'IconID':
                $data = (int)$data;
                break;
        }

        $method = 'set' . $element;
        $ctx = end($this->ctx);

        if (!empty($data) && method_exists($ctx, $method)) {
            $ctx->{$method}($data);
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
                $ctx = end($this->ctx);
                if (null === $times = $ctx->getTimes()) {
                    $times = new Times();
                    $ctx->setTimes($times);
                }
                $child->setContext($times);
                break;
            case $child instanceof EntryElementParser:
                $ctx = end($this->ctx);
                $child->setCtx($ctx->addEntry(new Entry()));
                break;
        }
    }

    /**
     * called on a closing tag of an element
     *
     * @param resource $parser
     * @param string $name
     * @param bool $isClose
     *
     * @return int
     */
    public function onElementClose($parser, $name, $isClose)
    {
        if ($this->getElement() === $name) {
            if (isset($this->ctx[$this->currentDepth])) {
                unset($this->ctx[$this->currentDepth]);
            }
            $this->currentDepth--;
        }
        return XmlParserState::STATE_FEED;
    }

    /**
     * called on a open tag of an element
     *
     * @param resource $parser
     * @param string $name
     * @param array $attributes
     *
     * @return int
     */
    public function onElementOpen($parser, $name, $attributes)
    {
        if ($this->getElement() === $name) {
            $this->currentDepth++;
            if ($this->currentDepth > 1 && $this->isValidDepth()) {
                $this->ctx[$this->currentDepth] = $this->ctx[$this->currentDepth-1]->addGroup(new Group());
            }
        }

        if ($name === 'Times' || $name === 'Entry' || $name === 'History') {
            return XmlParserState::STATE_FORWARD_CHILD;
        }

        return XmlParserState::STATE_FEED;
    }
}
