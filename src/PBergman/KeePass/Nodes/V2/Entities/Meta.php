<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;
use PBergman\KeePass\Nodes\V2\Traits\TimesTrait;

/**
 * Class Meta
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 */
class Meta extends AbstractNode
{
    /**
     * will return a validate schema for xml
     *
     * @return string
     */
    protected function getValidateSchema()
    {
        return '
        <xs:schema attributeFormDefault="unqualified" elementFormDefault="qualified" xmlns:xs="http://www.w3.org/2001/XMLSchema">
          <xs:element name="Meta">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="Generator"/>
                <xs:element type="xs:string" name="HeaderHash"/>
                <xs:element type="xs:string" name="DatabaseName"/>
                <xs:element type="xs:string" name="DatabaseNameChanged"/>
                <xs:element type="xs:string" name="DatabaseDescription"/>
                <xs:element type="xs:string" name="DatabaseDescriptionChanged"/>
                <xs:element type="xs:string" name="DefaultUserName"/>
                <xs:element type="xs:string" name="DefaultUserNameChanged"/>
                <xs:element type="xs:string" name="MaintenanceHistoryDays"/>
                <xs:element type="xs:string" name="Color"/>
                <xs:element type="xs:string" name="MasterKeyChanged"/>
                <xs:element type="xs:string" name="MasterKeyChangeRec"/>
                <xs:element type="xs:string" name="MasterKeyChangeForce"/>
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
                <xs:element name="CustomIcons">
                  <xs:complexType>
                    <xs:sequence>
                      <xs:element name="Icon" maxOccurs="unbounded" minOccurs="0">
                        <xs:complexType>
                          <xs:sequence>
                            <xs:element type="xs:string" name="UUID"/>
                            <xs:element type="xs:string" name="Data"/>
                          </xs:sequence>
                        </xs:complexType>
                      </xs:element>
                    </xs:sequence>
                  </xs:complexType>
                </xs:element>
                <xs:element type="xs:string" name="RecycleBinEnabled"/>
                <xs:element type="xs:string" name="RecycleBinUUID"/>
                <xs:element type="xs:dateTime" name="RecycleBinChanged"/>
                <xs:element type="xs:string" name="EntryTemplatesGroup"/>
                <xs:element type="xs:dateTime" name="EntryTemplatesGroupChanged"/>
                <xs:element type="xs:string" name="HistoryMaxItems"/>
                <xs:element type="xs:string" name="HistoryMaxSize"/>
                <xs:element type="xs:string" name="LastSelectedGroup"/>
                <xs:element type="xs:string" name="LastTopVisibleGroup"/>
                <xs:element name="Binaries" maxOccurs="unbounded" minOccurs="0" />
                <xs:element type="xs:string" name="CustomData"/>
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
        return [
            'Generator',
            'HeaderHash',
            'DatabaseName',
            'DatabaseNameChanged',
            'DatabaseDescription',
            'DatabaseDescriptionChanged',
            'DefaultUserName',
            'DefaultUserNameChanged',
            'MaintenanceHistoryDays',
            'Color',
            'MasterKeyChanged',
            'MasterKeyChangeRec',
            'MasterKeyChangeForce',
            'RecycleBinEnabled',
            'RecycleBinUUID',
            'RecycleBinChanged',
            'EntryTemplatesGroup',
            'EntryTemplatesGroupChanged',
            'HistoryMaxItems',
            'HistoryMaxSize',
            'LastSelectedGroup',
            'LastTopVisibleGroup',
            'CustomData',
        ];
    }

    /**
     * @return IconCollection
     */
    public function getCustomIcons()
    {
        return new IconCollection(
            $this->element->getElementsByTagName('CustomIcons')->item(0),
            $this->dom
        );
    }

    /**
     * @param   IconCollection $collection
     * @return  $this
     */
    public function setCustomIcons(IconCollection $collection)
    {
        $icons = $this->dom->createElement('CustomIcons');

        foreach ($collection as $element) {
            $icons->appendChild($this->dom->importNode($element->getElement()));
        }

        $this->element->replaceChild(
            $icons,
            $this->element->getElementsByTagName('CustomIcons')->item(0)
        );

        return $this;
    }

    /**
     * @return Binaries
     */
    public function getBinaries()
    {
        return new Binaries(
            $this->element->getElementsByTagName('Binaries')->item(0),
            $this->dom
        );
    }

    /**
     * @param   Binaries $binaries
     * @return  $this
     */
    public function setBinaries(Binaries $binaries)
    {
        $this->element->replaceChild(
            $binaries,
            $this->element->getElementsByTagName('Binaries')->item(0)
        );

        return $this;
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $meta = $this->dom->createElement('Meta');
        $meta->appendChild($this->dom->createElement('Generator', 'KeePass'));
        $meta->appendChild($this->dom->createElement('HeaderHash'));
        $meta->appendChild($this->dom->createElement('DatabaseName'));
        $meta->appendChild($this->dom->createElement('DatabaseNameChanged'));
        $meta->appendChild($this->dom->createElement('DatabaseDescription'));
        $meta->appendChild($this->dom->createElement('DatabaseDescriptionChanged'));
        $meta->appendChild($this->dom->createElement('DefaultUserName'));
        $meta->appendChild($this->dom->createElement('DefaultUserNameChanged'));
        $meta->appendChild($this->dom->createElement('MaintenanceHistoryDays'));
        $meta->appendChild($this->dom->createElement('Color'));
        $meta->appendChild($this->dom->createElement('MasterKeyChanged'));
        $meta->appendChild($this->dom->createElement('MasterKeyChangeRec'));
        $meta->appendChild($this->dom->createElement('MasterKeyChangeForce'));
        $meta->appendChild((new MemoryProtection(null, $this->dom))->getElement());
        $meta->appendChild($this->dom->createElement('CustomIcons'));
        $meta->appendChild($this->dom->createElement('RecycleBinEnabled'));
        $meta->appendChild($this->dom->createElement('RecycleBinUUID'));
        $meta->appendChild($this->dom->createElement('RecycleBinChanged'));
        $meta->appendChild($this->dom->createElement('EntryTemplatesGroup'));
        $meta->appendChild($this->dom->createElement('EntryTemplatesGroupChanged'));
        $meta->appendChild($this->dom->createElement('HistoryMaxItems'));
        $meta->appendChild($this->dom->createElement('HistoryMaxSize'));
        $meta->appendChild($this->dom->createElement('LastSelectedGroup'));
        $meta->appendChild($this->dom->createElement('LastTopVisibleGroup'));
        $meta->appendChild((new Binaries(null, $this->dom))->getElement());

    }

}
