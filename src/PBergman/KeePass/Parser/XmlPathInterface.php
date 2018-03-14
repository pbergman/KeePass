<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser;

/**
 * Interface XmlPathInterface
 *
 * @package PBergman\KeePass\Parser
 */
interface XmlPathInterface
{
    /**
     * enter a element from the xml tree
     *
     * @param string $element
     */
    public function enter($element);

    /**
     * leave the last entered element from the xml tree
     */
    public function leave();

    /**
     * return the current element name
     *
     * @return string|null
     */
    public function current();

    /**
     * get current depth of element
     *
     * @return int
     */
    public function depth();

    /**
     * @inheritdoc
     */
    public function __toString();
}
