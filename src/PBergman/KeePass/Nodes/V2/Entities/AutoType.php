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
 * @method  $this       setDataTransferObfuscation
 * @method  $this       setEnabled
 *
 * @method  string      getDataTransferObfuscation
 * @method  string      getEnabled
 */
class AutoType extends AbstractNode
{

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $string = $this->dom->createElement('AutoType');
        $string->appendChild($this->dom->createElement('Enabled'));
        $string->appendChild($this->dom->createElement('DataTransferObfuscation'));
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
          <xs:element name="AutoType">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="Enabled"/>
                <xs:element type="xs:string" name="DataTransferObfuscation"/>
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
        return ['Enabled', 'DataTransferObfuscation'];
    }
}