<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser;

use PBergman\KeePass\Exception\XmlParseException;
use PBergman\KeePass\Parser\Element\NoOpElementParser;

class XmlParser
{
    /** @var XmlElementParserInterface[] */
    protected $handlers;
    /** @var XmlElementParserInterface[][] */
    protected $active;
    /** @var XmlPathInterface  */
    protected $xpath;
    /** @var resource  */
    protected $parser;
    /** @var bool */
    protected $isDirty = true;

    /**
     * XmlParser constructor.
     *
     * @param XmlPathInterface $xpath
     * @param XmlElementParserInterface[] ...$elements
     */
    public function __construct(XmlPathInterface $xpath, XmlElementParserInterface ...$elements)
    {
        $this->xpath = $xpath;

        foreach ($elements as $element) {
            $this->register($element);
        }

        $this->init();
    }

    protected function init()
    {
        if (null === $this->parser) {
            $this->parser = xml_parser_create();
        }
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, true);
        xml_set_element_handler($this->parser, [$this, "onElementOpen"], [$this, 'onElementClose']);
        xml_set_character_data_handler($this->parser, [$this, "onData"]);

    }

    /**
     * @return null|XmlElementParserInterface
     */
    protected function getHandlerForElement($element)
    {
        foreach ($this->handlers as $handler) {
            if ($element === $handler->getElement()) {
                return $handler;
            }
        }
        return null;
    }

    /**
     * @return XmlElementParserInterface[]
     */
    protected function getCurrentHandler()
    {
        return count($this->active) > 0 ? end($this->active) : [-1, null, true];
    }

    /**
     * @param XmlElementParserInterface $handler
     * @return false|int
     */
    protected function getActiveIndex(XmlElementParserInterface $handler)
    {
        return array_search($handler, array_column($this->active, 1), true);
    }

    /**
     * @param string $element
     * @return null|XmlElementParserInterface
     */
    protected function nextHandlerFor($element)
    {
        if (null !== $handler = $this->getHandlerForElement($element)) {
            $this->active[] = [$this->xpath->depth(), $handler, true];
        }

        return $handler;
    }

    protected function nextChild($element = null)
    {
        if (is_null($element)) {
            $element = $this->xpath->current();
        }

        if (null === $handler = $this->getHandlerForElement($element)) {
            $handler = new NoOpElementParser($element);
        }

        for ($i = count($this->active) - 1; $i >= 0; $i--) {
            if ($this->active[$i][1] instanceof XmlElementRootInterface) {
                $this->active[$i][1]->initChild($handler);
                break;
            }
        }

        $this->active[] = [$this->xpath->depth(), $handler, true];
    }

    /**
     * @param resource $parser
     * @param string $data
     */
    public function onData($parser, $data)
    {
        list(, $handler, $active) = $this->getCurrentHandler();

        if ($active && $handler instanceof XmlDataHandlerInterface) {
            $this->handleState($handler->onData(trim($data)), $handler);
        }
    }

    /**
     * @param int $state
     * @return bool
     */
    protected function isFinished($state)
    {
        return XmlParserState::STATE_FINISHED === (XmlParserState::STATE_FINISHED & $state);
    }

    /**
     * @param int $state
     * @return bool
     */
    protected function isForwardChild($state)
    {
        return XmlParserState::STATE_FORWARD_CHILD === (XmlParserState::STATE_FORWARD_CHILD & $state);
    }

    /**
     * @inheritdoc
     */
    public function onElementClose($parser, $name)
    {
        list($depth, $handler, $active) = $this->getCurrentHandler();

        if (($isClose = ($this->xpath->depth() === $depth)) && !is_null($handler)) {
            if ($handler instanceof XmlElementRootInterface && $this->isFinished($handler->getState())) {
                $this->remove($handler);
            }
            array_pop($this->active);
        }

        if ($active && $handler instanceof XmlElementHandlerInterface) {
            $this->handleState($handler->onElementClose($parser, $name, $isClose), $handler);
        }

        $this->xpath->leave();
    }

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attributes)
    {
        $this->xpath->enter($name);

        list(, $handler, $state) = $this->getCurrentHandler();

        if (null === $handler) {
            $handler = $this->nextHandlerFor($name);
        }

        if (null !== $handler && $state && $handler instanceof XmlElementHandlerInterface) {
            $this->handleState($handler->onElementOpen($parser, $name, $attributes), $handler);
        }
    }

    /**
     * @param int $state
     * @param XmlElementParserInterface $handler
     */
    protected function handleState($state, XmlElementParserInterface $handler)
    {
        if ($this->isForwardChild($state)) {
            $this->nextChild();
        }

        if ($this->isFinished($state) && false !== $index = $this->getActiveIndex($handler)) {
            $this->active[$index][2] = false;
        }
    }

    /**
     * @param XmlElementParserInterface $parser
     */
    public function register(XmlElementParserInterface $parser)
    {
        $parser->init($this->xpath);
        $this->handlers[] = $parser;
        $this->isDirty = true;
    }

    /**
     * @param XmlElementParserInterface $parser
     */
    public function remove(XmlElementParserInterface $parser)
    {
        if (false !== $index = array_search($parser, $this->handlers, true)) {
            unset($this->handlers[$index]);
            $this->isDirty = true;
        }
    }

    /**
     * @return bool
     */
    protected function hasRootElements()
    {
        static $count;
        if (is_null($count) || $this->isDirty) {
            $count = count(
                array_filter(
                    $this->handlers,
                    function ($e) {
                        return $e instanceof XmlElementRootInterface;
                    }
                )
            );
        }
        return $count > 0;
    }

    /**
     * @inheritdoc
     */
    public function parse($data, $force = false)
    {
        if (false === $force && !$this->hasRootElements()) {
            return XmlParserState::STATE_FINISHED;
        }

        if (false === xml_parse($this->parser, $data)) {
            throw new XmlParseException($this->parser);
        }

        return XmlParserState::STATE_FEED;
    }
}