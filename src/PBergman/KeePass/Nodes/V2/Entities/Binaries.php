<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;
use PBergman\KeePass\Nodes\V2\Traits\TimesTrait;

/**
 * Class Binaries
 *
 * @package PBergman\KeePass\Nodes\V2
 */
class Binaries extends AbstractNode implements \ArrayAccess, \Countable, \Iterator
{
    /** @var \DOMXPath  */
    protected $xpath;
    /** @var int */
    protected $pos = 0;


    /**
     * @param \DomNode      $element
     * @param \DOMDocument  $dom
     * @param bool          $validate
     */
    function __construct(\DomNode $element = null, \DOMDocument $dom = null, $validate = true)
    {
        parent::__construct($element, $dom, $validate);
        $this->xpath = new \DOMXPath($this->dom);
    }


    /**
     * should return array of properties of the dom
     * that can be accessed by the __call method,
     *
     * @return array
     */
    protected function getProperties()
    {
        return [];
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $this->dom->createElement('Binaries');

    }

    /**
     * will return a validate schema for xml
     *
     * @return string
     */
    protected function getValidateSchema()
    {
        return '
        <xs:schema attributeFormDefault="unqualified" elementFormDefault="qualified" xmlns:xs="http://www.w3.org/2001/XMLSchema">
          <xs:element name="Binaries">
            <xs:complexType>
              <xs:sequence>
                <xs:element name="Binary" maxOccurs="unbounded" minOccurs="0">
                  <xs:complexType>
                    <xs:simpleContent>
                      <xs:extension base="xs:string">
                        <xs:attribute type="xs:string" name="Compressed" use="optional"/>
                        <xs:attribute type="xs:string" name="ID" use="optional"/>
                      </xs:extension>
                    </xs:simpleContent>
                  </xs:complexType>
                </xs:element>
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>
        ';
    }

    /**
     * @param   \DomNode $node
     * @return  string
     */
    protected function unPack(\DomNode $node)
    {
        $data = base64_decode($node->nodeValue);

        if ($node->attributes->getNamedItem('Compressed')->nodeValue === 'True') {
            $data = gzdecode($data);
        }

        return $data;
    }


    /**
     * @param   string $data
     * @return  $this
     */
    protected function pack(\DomNode $node, $data)
    {
        $data = gzencode($data);
        $data = base64_encode($data);
        $node->attributes->getNamedItem('Compressed')->nodeValue = 'True';
        $node->textContent = $data;
        return $this;
    }

    /*
     * @inheritdoc
     */
    public function current()
    {
        $item = $this
            ->xpath
            ->query('Binary', $this->element)
            ->item($this->pos);


        if (!is_null($item)) {
            return $this->unPack($item);
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
        $element = $this
            ->xpath
            ->query('Binary', $this->element)
            ->item($this->pos);

        if (!is_null($element)) {
            return $element->attributes->getNamedItem('ID')->nodeValue;
        } else {
            return null;
        }

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
        return $this->xpath->query(sprintf('Binary[@ID="%d"]', $offset), $this->element)->length > 0;
    }

    /*
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        $element = $this->xpath->query(sprintf('Binary[@ID="%d"]', $offset), $this->element);

        if (!is_null($element)) {
            return $this->unPack($element->item(0));
        } else {
            return null;
        }
    }

    /*
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $element = $this->xpath->query(sprintf('Binary[@ID="%d"]', $offset), $this->element)->item(0);

        if (!is_null($element)) {
            $this->pack($element, $value);
        } else {
            $binary = $this->dom->createElement('Binary');
            $binary->setAttribute('ID', $offset);
            $binary->setAttribute('Compressed', 'True');
            $binary->textContent = base64_encode(gzencode($value));
            $this->element->appendChild($binary);
        }
    }

    /*
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $element = $this->xpath->query(sprintf('Binary[@ID="%d"]', $offset), $this->element);

        if (!is_null($element)) {
            $this->element->removeChild($element->item(0));
        }
    }

    /*
     * @inheritdoc
     */
    public function count()
    {
        return $this->xpath->query('Binary', $this->element)->length;
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
            $return[$this->key()] = $item;
            $this->next();
        }
        return $return;
    }

}