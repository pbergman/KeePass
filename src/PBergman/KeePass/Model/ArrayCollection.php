<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Model;

/**
 * Class ArrayCollection
 *
 * @package PBergman\KeePass\Model
 */
class ArrayCollection implements \Countable, \Iterator, \ArrayAccess
{
    /** @var array  */
    private $elements;

    /**
     * Collection constructor.
     *
     * @param array ...$element
     */
    public function __construct(...$element)
    {
        $this->elements = $element;
    }

    public function reset()
    {
        $this->elements = [];
    }

    public function set(array $data)
    {
        $this->elements = $data;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->elements;
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * @param mixed $element
     * @return bool
     */
    public function has($element)
    {
        return false !== array_search($element, $this->elements, true);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return isset($this->elements[$offset]) ? $this->elements[$offset] : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (!empty($offset)) {
            $this->elements[$offset] = $value;
        } else {
            $this->elements[] = $value;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->elements[$offset])) {
            unset($this->elements[$offset]);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        return next($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return null !== key($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->elements);
    }
}