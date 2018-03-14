<?php
namespace PBergman\KeePass\Parser;

/**
 * Interface XmlElementHandlerInterface
 *
 * @package PBergman\KeePass\Parser
 */
interface XmlElementHandlerInterface
{
    /**
     * called on a closing tag of an element
     *
     * @param resource $parser
     * @param string $name
     * @param bool $isClose
     *
     * @return int
     */
    public function onElementClose($parser, $name, $isClose);

    /**
     * called on a open tag of an element
     *
     * @param resource $parser
     * @param string $name
     * @param array $attributes
     *
     * @return int
     */
    public function onElementOpen($parser, $name, $attributes);
}