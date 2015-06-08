<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;
use PBergman\KeePass\Nodes\V2\Traits\TimesTrait;

/**
 * Class Entry
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 * @method string   getUUID
 * @method string   getIconID
 * @method string   getForegroundColor
 * @method string   getBackgroundColor
 * @method string   getOverrideURL
 *
 * @method $this    setUUID
 * @method $this    setIconID
 * @method $this    setForegroundColor
 * @method $this    setBackgroundColor
 * @method $this    setOverrideURL
 */
class Entry extends AbstractNode
{
    use TimesTrait;

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
           'IconID',
           'ForegroundColor',
           'BackgroundColor',
           'OverrideURL',
       ];
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $entry = $this->dom->createElement('Group');
        $entry->appendChild($this->dom->createElement('UUID'));
        $entry->appendChild($this->dom->createElement('IconID'));
        $entry->appendChild($this->dom->createElement('ForegroundColor'));
        $entry->appendChild($this->dom->createElement('BackgroundColor'));
        $entry->appendChild($this->dom->createElement('OverrideURL'));
        $entry->appendChild($this->dom->createElement('Tags'));
        $entry->appendChild((new Times(null, $this->dom))->getElement());
        $entry->appendChild((new String(null, $this->dom))->getElement());
        $entry->appendChild((new AutoType(null, $this->dom))->getElement());
//        $entry->appendChild($this->dom->createElement('History'));

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
          <xs:element name="Entry">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="UUID"/>
                <xs:element type="xs:string" name="IconID"/>
                <xs:element type="xs:string" name="ForegroundColor"/>
                <xs:element type="xs:string" name="BackgroundColor"/>
                <xs:element type="xs:string" name="OverrideURL"/>
                <xs:element type="xs:string" name="Tags"/>
                <xs:element name="Times" maxOccurs="1" />
                <xs:element name="String" maxOccurs="unbounded" minOccurs="0" />
                <xs:element name="AutoType" maxOccurs="unbounded" minOccurs="0" />
                <xs:element name="History" maxOccurs="unbounded" minOccurs="0" />
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>
        ';
    }

    /**
     * returns the tags as array
     *
     * @return array
     */
    public function getTags()
    {
        $tags = $this
            ->element
            ->getElementsByTagName('Tags')
            ->item(0)
            ->textContent;

        return explode(';', $tags);
    }

    /**
     * Add tags to xml tree
     *
     * @param   array $tags
     * @return  $this
     */
    public function setTags(array $tags)
    {
        $this->element->getElementsByTagName('Tags')->item(0)->textContent = implode(';', $tags);
        return $this;
    }

    /**
     * @return AutoType
     */
    public function getAutoType()
    {
        return new AutoType(
            $this->element->getElementsByTagName('AutoType')->item(0),
            $this->dom
        );
    }

    /**
     * @param   AutoType $autoType
     * @return  $this
     */
    public function setAutoType(AutoType $autoType)
    {
        $this->element->replaceChild(
            $this->dom->importNode($autoType->getElement(), true),
            $this->element->getElementsByTagName('AutoType')->item(0)
        );

        return $this;
    }

    /**
     * get all string elements
     *
     * @return null|StringCollection|String[]
     */
    public function getStrings()
    {
        return new StringCollection(
            $this->element,
            $this->dom
        );
    }
}

