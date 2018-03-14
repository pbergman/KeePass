<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Node;

use PBergman\KeePass\Model\ArrayCollection;
use PBergman\KeePass\Model\BinaryString;

class Entry
{
    /** @var BinaryString */
    private $uuid;
    /** @var int */
    private $iconID;
    /** @var string */
    private $foregroundColor;
    /** @var string */
    private $backgroundColor;
    /** @var string */
    private $overrideURL;
    /** @var string */
    private $tags;
    /** @var Times */
    private $times;
    /** @var ArrayCollection */
    private $values;

    /**
     * Entry constructor.
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * @return BinaryString
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * @param BinaryString $uuid
     */
    public function setUUID(BinaryString $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return int
     */
    public function getIconID()
    {
        return $this->iconID;
    }

    /**
     * @param int $iconID
     */
    public function setIconID($iconID)
    {
        $this->iconID = $iconID;
    }

    /**
     * @return string
     */
    public function getForegroundColor()
    {
        return $this->foregroundColor;
    }

    /**
     * @param string $foregroundColor
     */
    public function setForegroundColor($foregroundColor)
    {
        $this->foregroundColor = $foregroundColor;
    }

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param string $backgroundColor
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * @return string
     */
    public function getOverrideURL()
    {
        return $this->overrideURL;
    }

    /**
     * @param string $overrideURL
     */
    public function setOverrideURL($overrideURL)
    {
        $this->overrideURL = $overrideURL;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return Times
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @param Times $times
     * @return Times
     */
    public function setTimes($times)
    {
        $this->times = $times;
        return $times;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->values->has($key) ? $this->values[$key] : $default;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @return ArrayCollection
     */
    public function getValues()
    {
        return $this->values;
    }
}