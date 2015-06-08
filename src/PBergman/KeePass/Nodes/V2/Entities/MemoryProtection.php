<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;

/**
 * Class MemoryProtection
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 * @method bool getProtectTitle
 * @method bool getProtectUserName
 * @method bool getProtectPassword
 * @method bool getProtectURL
 * @method bool getProtectNotes
 *
 * @method $this setProtectTitle
 * @method $this setProtectUserName
 * @method $this setProtectPassword
 * @method $this setProtectURL
 * @method $this setProtectNotes
 */
class MemoryProtection extends AbstractNode
{
    /**
     * should return array of properties of the dom
     * that can be accessed by the __call method,
     *
     * @return array
     */
    protected function getProperties()
    {
        return [
            'ProtectTitle',
            'ProtectUserName',
            'ProtectPassword',
            'ProtectURL',
            'ProtectNotes',
        ];
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $mp = $this->dom->createElement('MemoryProtection');
        $mp->appendChild($this->dom->createElement('ProtectTitle', 'False'));
        $mp->appendChild($this->dom->createElement('ProtectUserName', 'False'));
        $mp->appendChild($this->dom->createElement('ProtectPassword', 'True'));
        $mp->appendChild($this->dom->createElement('ProtectURL', 'False'));
        $mp->appendChild($this->dom->createElement('ProtectNotes', 'False'));
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
          <xs:element name="MemoryProtection">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="ProtectTitle"/>
                <xs:element type="xs:string" name="ProtectUserName"/>
                <xs:element type="xs:string" name="ProtectPassword"/>
                <xs:element type="xs:string" name="ProtectURL"/>
                <xs:element type="xs:string" name="ProtectNotes"/>
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>
        ';
    }
}