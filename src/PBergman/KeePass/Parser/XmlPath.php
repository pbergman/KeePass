<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser;

/**
 * Class XmlPath
 *
 * @package PBergman\KeePass\Parser
 */
class XmlPath implements XmlPathInterface
{
    /** @var array */
    private $path;

    /**
     * @inheritdoc
     */
    public function enter($element)
    {
        $this->path[] = $element;
    }

    /**
     * @inheritdoc
     */
    public function leave()
    {
        array_pop($this->path);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->depth() > 0 ? end($this->path) : null;
    }

    /**
     * @inheritdoc
     */
    public function depth()
    {
        return count($this->path);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return implode("/", $this->path);
    }
}
