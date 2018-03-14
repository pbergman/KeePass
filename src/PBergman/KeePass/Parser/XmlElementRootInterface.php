<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser;

interface XmlElementRootInterface
{
    /**
     * @return int
     */
    public function getState();

    /**
     * @param XmlElementParserInterface $child
     */
    public function initChild(XmlElementParserInterface $child);
}