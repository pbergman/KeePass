<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlPathInterface;

class NoOpElementParser implements XmlElementParserInterface
{
    /** @var string */
    protected $element;

    /**
     * NoOpElementParser constructor.
     *
     * @param string $element
     */
    public function __construct($element)
    {
       $this->element = $element;
    }


    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $tracer)
    {

    }

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        $this->element;
    }
}