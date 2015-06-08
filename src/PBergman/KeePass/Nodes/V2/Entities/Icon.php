<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;
use PBergman\KeePass\Nodes\V2\Traits\TimesTrait;

/**
 * Class Icons
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 * @method $this setUUID
 * @method $this setData
 *
 * @method string getUUID
 * @method string getData
 */
class Icon extends AbstractNode
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
            'UUID',
            'Data',
        ];
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $mp = $this->dom->createElement('Icon');
        $mp->appendChild($this->dom->createElement('UUID'));
        $mp->appendChild($this->dom->createElement('Data'));
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
          <xs:element name="Icon">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="UUID"/>
                <xs:element type="xs:string" name="Data"/>
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>
        ';
    }
}