<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

/**
 * Class AbstractNode
 *
 * @package PBergman\KeePass\Nodes\V2
 */
abstract class AbstractNode
{
    /** @var \SimpleXMLElement */
    protected $element;

    /**
     * @param \SimpleXMLElement $element
     */
    function __construct(\SimpleXMLElement $element = null)
    {
        if (is_null($element)) {
            $element = $this->getDefaultElement();
            if ($element instanceof \SimpleXMLElement) {
                $this->element = $element;
            } else {
                throw new \RuntimeException('The method (getDefaultElement) should return a instance of SimpleXMLElement');
            }

        } else {
            $this->element = $element;
        }
    }

    /**
     * returns the default xml that specifies this node
     *
     * @return \SimpleXMLElement
     */
    abstract protected function getDefaultElement();

    /**
     * Wraps in dom element so it wont wrap <XML tags
     * around as SimpleXMLElement::toXml does
     *
     * @return string
     */
    public function __toString()
    {
        if (false !== $dom = dom_import_simplexml($this->element)) {
            return $dom->C14N();
        } else {
            throw new \RuntimeException('Could not import SimpleXMLElement');
        }
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getElement()
    {
        return $this->element;
    }
}