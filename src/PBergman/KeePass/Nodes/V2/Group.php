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
    const ROOT_ELEMENT_NAME = 'Group';

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $group = $this->dom->createElement(self::ROOT_ELEMENT_NAME);
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
                <xs:element type="xs:string" name="Name"/>
                <xs:element type="xs:string" name="Notes"/>
                <xs:element type="xs:string" name="IconID"/>
                <xs:element name="Times" />
                <xs:element type="xs:string" name="IsExpanded"/>
                <xs:element type="xs:string" name="DefaultAutoTypeSequence"/>
                <xs:element type="xs:string" name="EnableAutoType"/>
                <xs:element type="xs:string" name="EnableSearching"/>
                <xs:element type="xs:string" name="LastTopVisibleEntry"/>
                <xs:element name="Entry" maxOccurs="unbounded" minOccurs="0" />
                <xs:element name="Group" maxOccurs="unbounded" minOccurs="0" />
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>
        ';
    }

    /**
     * @param   string      $name
     * @param   array       $arguments
     * @return $this|string
     */
    public function __call($name, $arguments)
    {

        if (preg_match('#^(?P<method>get|set)(?P<name>.+)$#', $name, $ret)) {
            switch ($ret['method']) {
                case 'get':
                    $value = $this->element->getElementsByTagName($ret['name'])->item(0)->textContent;
                    switch($ret['name']) {
                        case 'UUID':
                        case 'Name':
                        case 'Notes':
                        case 'IconID':
                        case 'DefaultAutoTypeSequence':
                        case 'EnableAutoType':
                        case 'EnableSearching':
                        case 'LastTopVisibleEntry':
                            return $value;
                            break;
                        case 'IsExpanded':
                            return ($value === 'True') ? true : false;
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));

                    }
                    break;
                case 'set':
                    switch($ret['name']) {
                        case 'UUID':
                        case 'Name':
                        case 'Notes':
                        case 'IconID':
                        case 'DefaultAutoTypeSequence':
                        case 'EnableAutoType':
                        case 'EnableSearching':
                        case 'LastTopVisibleEntry':
                            $value =  $arguments[0];
                            break;
                        case 'IsExpanded':
                            $value = $this->stringify($arguments[0]);
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));

                    }
                    $this->element->getElementsByTagName($ret['name'])->item(0)->textContent = $value;
                    return $this;
                    break;
            }
        } else {
            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));
        }
    }

    /**
     * @return Times
     */
    public function getTimes()
    {
        return new Times(
            $this->element->getElementsByTagName('Times')->item(0),
            $this->dom
        );
    }

    /**
     * @param   Times $times
     *
     * @return  $this
     */
    public function setTimes(Times $times)
    {
        $this->element->replaceChild(
            $this->dom->importNode($times->getElement(), true),
            $this->element->getElementsByTagName('Times')->item(0)
        );

        return $this;
    }
}