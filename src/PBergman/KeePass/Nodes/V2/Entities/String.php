<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;

/**
 * Class String
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 * @method  $this       setValue
 * @method  $this       setKey
 *
 * @method  string      getValue
 * @method  string      getKey
 */
class String extends AbstractNode
{

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $string = $this->dom->createElement('String');
        $string->appendChild($this->dom->createElement('Key'));
        $string->appendChild($this->dom->createElement('Value'));
        return $string;
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
          <xs:element name="String">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="Key"/>
                <xs:element name="Value">
                  <xs:complexType>
                    <xs:simpleContent>
                      <xs:extension base="xs:string">
                        <xs:attribute type="xs:string" name="Protected"/>
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
     * should return array of properties of the dom
     * that can be accessed by the __call method,
     *
     * @return array
     */
    protected function getProperties()
    {
        return ['Key', 'Value'];
    }
}