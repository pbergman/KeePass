<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser;

use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlPathInterface;
use PBergman\KeePass\Parser\XmlPathTrace;

interface XmlElementParserInterface
{
    /**
     * @return string
     */
    public function getElement();

    /**
     * this method will be called when a parser
     * is registered by the parser.
     *
     * @param XmlPathInterface $xpath
     */
    public function init(XmlPathInterface $xpath);
}