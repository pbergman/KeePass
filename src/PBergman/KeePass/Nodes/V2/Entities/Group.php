<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;
use PBergman\KeePass\Nodes\V2\Traits\TimesTrait;

/**
 * Class String
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 * @method  $this       setUUID
 * @method  $this       setName
 * @method  $this       setNotes
 * @method  $this       setIconID
 * @method  $this       setDefaultAutoTypeSequence
 * @method  $this       setEnableAutoType
 * @method  $this       setEnableSearching
 * @method  $this       setLastTopVisibleEntry
 * @method  $this       setIsExpanded
 *
 * @method  string      getUUID
 * @method  string      getName
 * @method  string      getNotes
 * @method  string      getIconID
 * @method  string      getDefaultAutoTypeSequence
 * @method  string      getEnableAutoType
 * @method  string      getEnableSearching
 * @method  string      getLastTopVisibleEntry
 * @method  bool        getIsExpanded
 */
class Group extends AbstractNode
{
    use TimesTrait;

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $group = $this->dom->createElement('Group');
        $group->appendChild($this->dom->createElement('UUID'));
        $group->appendChild($this->dom->createElement('Name'));
        $group->appendChild($this->dom->createElement('Notes'));
        $group->appendChild($this->dom->createElement('IconID'));
        $group->appendChild((new Times(null, $this->dom))->getElement());
        $group->appendChild($this->dom->createElement('IsExpanded', $this->stringify(true)));
        $group->appendChild($this->dom->createElement('DefaultAutoTypeSequence'));
        $group->appendChild($this->dom->createElement('EnableAutoType', $this->stringify(null)));
        $group->appendChild($this->dom->createElement('EnableSearching', $this->stringify(null)));
        $group->appendChild($this->dom->createElement('LastTopVisibleEntry'));
        return $group;
    }

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
            'Name',
            'Notes',
            'IconID',
            'IsExpanded',
            'DefaultAutoTypeSequence',
            'EnableAutoType',
            'EnableSearching',
            'LastTopVisibleEntry',
        ];
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
          <xs:element name="Group">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="UUID" />
                <xs:element type="xs:string" name="Name" />
                <xs:element type="xs:string" name="Notes" />
                <xs:element type="xs:string" name="IconID" />
                <xs:element name="Times" maxOccurs="1" />
                <xs:element type="xs:string" name="IsExpanded" />
                <xs:element type="xs:string" name="DefaultAutoTypeSequence" />
                <xs:element type="xs:string" name="EnableAutoType" />
                <xs:element type="xs:string" name="EnableSearching" />
                <xs:element type="xs:string" name="LastTopVisibleEntry" />
                <xs:element name="Entry" maxOccurs="unbounded" minOccurs="0" />
                <xs:element name="Group" maxOccurs="unbounded" minOccurs="0" />
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>
        ';
    }
}