<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Entities;

use PBergman\KeePass\Nodes\V2\AbstractNode;

/**
 * Class Times
 *
 * @method $this setCreationTime
 * @method $this setLastModificationTime
 * @method $this setLastAccessTime
 * @method $this setExpiryTime
 * @method $this setLocationChanged
 * @method $this setExpires
 * @method $this setUsageCount
 *
 * @method \DateTime getCreationTime
 * @method \DateTime getLastModificationTime
 * @method \DateTime getLastAccessTime
 * @method \DateTime getExpiryTime
 * @method \DateTime getLocationChanged
 * @method bool      getExpires
 * @method int       getUsageCount
 *
 * @package PBergman\KeePass\Nodes\V2
 */
class Times extends AbstractNode
{
    const DATE_FORMAT = "Y-m-d\TH:i:se";

    /**
     * @inheritdoc
     */
    public function __call($name, $arguments)
    {
        if (preg_match('#^(?P<method>get|set)(?P<name>\w+(Time|Changed))$#', $name, $ret)) {
            switch($ret['method']) {
                case 'get':
                    switch($name) {
                        case 'getCreationTime':
                        case 'getLastModificationTime':
                        case 'DefaultUserNameChanged':
                        case 'getLastAccessTime':
                        case 'getExpiryTime':
                        case 'getLocationChanged':
                            $value = $this
                                ->element
                                ->getElementsByTagName($ret['name'])
                                ->item(0)
                                ->nodeValue;
                            return new \DateTime($value);
                            break;
                    }
                    break;
                case 'set':
                    if ($arguments[0] instanceof \DateTime) {
                        $arguments[0] = $arguments[0]
                            ->setTimezone(new \DateTimeZone('Z'))
                            ->format(Times::DATE_FORMAT);
                    }
                    break;
            }

        }

        return parent::__call($name, $arguments);
    }


    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    protected function buildDefaultDomElement()
    {
        $times = $this->dom->createElement('Times');
        $times->appendChild($this->dom->createElement('CreationTime', $this->getNowTimeStamp()));
        $times->appendChild($this->dom->createElement('LastModificationTime'));
        $times->appendChild($this->dom->createElement('LastAccessTime'));
        $times->appendChild($this->dom->createElement('ExpiryTime'));
        $times->appendChild($this->dom->createElement('Expires', $this->stringify(false)));
        $times->appendChild($this->dom->createElement('UsageCount', 0));
        $times->appendChild($this->dom->createElement('LocationChanged'));
        return $times;
    }

    /**
     * @return string
     */
    protected function getNowTimeStamp()
    {
        return (new \DateTime('now' , new \DateTimeZone('Z')))->format(self::DATE_FORMAT);
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
          <xs:element name="Times">
            <xs:complexType>
              <xs:sequence>
                <xs:element type="xs:string" name="CreationTime"/>
                <xs:element type="xs:string" name="LastModificationTime"/>
                <xs:element type="xs:string" name="LastAccessTime"/>
                <xs:element type="xs:string" name="ExpiryTime"/>
                <xs:element type="xs:string" name="Expires"/>
                <xs:element type="xs:string" name="UsageCount"/>
                <xs:element type="xs:string" name="LocationChanged"/>
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
            'CreationTime',
            'LastModificationTime',
            'LastAccessTime',
            'ExpiryTime',
            'UsageCount',
            'LocationChanged',
        ];
    }
}
