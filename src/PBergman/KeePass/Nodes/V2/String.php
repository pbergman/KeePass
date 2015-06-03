<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

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
    const ROOT_ELEMENT_NAME = 'String';

    /**
     * @param   string      $name
     * @param   array       $arguments
     * @return $this|string
     */
    public function __call($name, $arguments)
    {
        if (preg_match('#^(?P<method>get|set)(?P<name>Key|Value)$#', $name, $ret)) {
            switch ($ret['method']) {
                case 'get':
                    return (string) $this->element->getElementsByTagName($ret['name'])->item(0)->textContent;
                    break;
                case 'set':
                    $this->element->getElementsByTagName($ret['name'])->item(0)->textContent = $arguments[0];
                    return $this;
                    break;
            }
        } else {
            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));
        }
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $string = $this->dom->createElement(self::ROOT_ELEMENT_NAME);
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
          <xs:element name="Strings">
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
}