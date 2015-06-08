<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;
use PBergman\KeePass\Nodes\V2\Traits\TimesTrait;

/**
 * Class IconCollection
 *
 * @package PBergman\KeePass\Nodes\V2
 */
class IconCollection implements \ArrayAccess, \Countable, \Iterator
{
    /** @var \DomNode */
    protected $parent_node;
    /** @var \DOMDocument */
    protected $dom;
    /** @var  int */
    protected $pos = 0;
    /** @var \DOMXPath  */
    protected $xpath;


    /**
     * @param \DOMNode      $parentNode
     * @param \DOMDocument  $dom
     */
    function __construct(\DOMNode $parentNode, \DOMDocument $dom)
    {
        $this->parent_node = $parentNode;
        $this->dom = $dom;
        $this->xpath = new \DOMXPath($this->dom);
    }

    /*
     * @inheritdoc
     */
    public function current()
    {
        $item = $this
            ->xpath
            ->query('Icon', $this->parent_node)
            ->item($this->pos);

        if (!is_null($item)) {
            return new Icon($item, $this->dom);
        } else {
            return null;
        }
    }

    /*
     * @inheritdoc
     */
    public function next()
    {
        $this->pos++;
    }

    /*
     * @inheritdoc
     */
    public function key()
    {
        return  $this
            ->xpath
            ->query(sprintf('Icon[%s]/UUID', $this->pos), $this->parent_node)
            ->item(0)
            ->textContent;
    }

    /*
     * @inheritdoc
     */
    public function valid()
    {
        return $this->pos < $this->count();
    }

    /*
     * @inheritdoc
     */
    public function rewind()
    {
        $this->pos = 0;
    }

    /*
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return (bool) $this
            ->xpath
            ->query(sprintf('Icon/UUID[text()="%s"]', $offset), $this->parent_node)
            ->length;
    }

    /*
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        $item =  $this
            ->xpath
            ->query(sprintf('Icon/UUID[text()="%s"]/../Data', $offset), $this->parent_node)
            ->item(0);

        if (is_null($item)) {
            return null;
        } else {
            return $item->textContent;
        }

    }

    /*
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $item = $this
            ->xpath
            ->query(sprintf('Icon/UUID[text()="%s"]/../Data', $offset), $this->parent_node)
            ->item(0);

        if (is_null($item)) {
            $icon = new Icon(null, $this->dom);
            $icon
                ->setUUID($offset)
                ->setData($value);
            $this
                ->parent_node
                ->appendChild($icon->getElement());
        } else {
            $item->textContent = $value;
        }


    }

    /*
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $item = $this
            ->xpath
            ->query(sprintf('Icon/UUID[text()="%s"]/..', $offset), $this->parent_node)
            ->item(0);

        if (!is_null($item)) {
            $this->parent_node->removeChild($item);
        }
    }

    /*
     * @inheritdoc
     */
    public function count()
    {
        return $this->xpath->query('Icon', $this->parent_node)->length;
    }

    /**
     * get string collection to "associative" array
     *
     * @return array
     */
    public function toArray()
    {
        $return  = [];
        $this->rewind();
        while($this->valid()) {
            /** @var \PBergman\KeePass\Nodes\V2\Entities\Icon $item */
            $item = $this->current();
            $return[$item->getUUID()] = $item->getData();
            $this->next();
        }
        return $return;
    }
}