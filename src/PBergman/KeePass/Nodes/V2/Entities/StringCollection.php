<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

/**
 * Class StringCollection
 *
 * @package PBergman\KeePass\Nodes\V2\Entities
 */
class StringCollection implements \ArrayAccess, \Countable, \Iterator
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
            ->query('String', $this->parent_node)
            ->item($this->pos);

        if (!is_null($item)) {
            return new String($item, $this->dom);
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
            ->query(sprintf('String[%s]/Key', $this->pos), $this->parent_node)
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
            ->query(sprintf('String/Key[text()="%s"]', $offset), $this->parent_node)
            ->length;
    }

    /*
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        $item =  $this
            ->xpath
            ->query(sprintf('String/Key[text()="%s"]/../Value', $offset), $this->parent_node)
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
            ->query(sprintf('String/Key[text()="%s"]/../Value', $offset), $this->parent_node)
            ->item(0);

        if (is_null($item)) {
            $autoType =  $this
                ->xpath
                ->query('AutoType', $this->parent_node)
                ->item(0);

            $item = new String(null, $this->dom);
            $item
                ->setKey($offset)
                ->setValue($value);
            $this
                ->parent_node
                ->insertBefore($item->getElement(), $autoType);
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
            ->query(sprintf('String/Key[text()="%s"]/..', $offset), $this->parent_node)
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
        return $this->xpath->query('String', $this->parent_node)->length;
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
            /** @var \PBergman\KeePass\Nodes\V2\Entities\String $item */
            $item = $this->current();
            $return[$item->getKey()] = $item->getValue();
            $this->next();
        }
        return $return;
    }
}