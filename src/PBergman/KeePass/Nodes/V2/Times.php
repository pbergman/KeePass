<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

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
    const ROOT_ELEMENT_NAME = 'Times';

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
                        case 'CreationTime':
                        case 'LastModificationTime':
                        case 'LastAccessTime':
                        case 'ExpiryTime':
                        case 'LocationChanged':
                            return new \DateTime($value);
                            break;
                        case 'Expires':
                            return ($value === 'True') ? true : false;
                            break;
                        case 'UsageCount':
                            return $value ;
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));

                    }
                    break;
                case 'set':
                    switch($ret['name']) {
                        case 'CreationTime':
                        case 'LastModificationTime':
                        case 'LastAccessTime':
                        case 'ExpiryTime':
                        case 'LocationChanged':
                            if (!$arguments[0] instanceof \DateTime) {
                                throw new \InvalidArgumentException(sprintf('Given argument should be a type of DateTime give "%s"', gettype($arguments[0])));
                            } else {
                                $arguments[0] = $arguments[0]
                                    ->setTimezone(new \DateTimeZone('Z'))
                                    ->format(self::DATE_FORMAT);
                            }
                            break;
                        case 'Expires':
                        case 'UsageCount':
                            $arguments[0] = $this->stringify($arguments[0]);
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));

                    }
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
        $times = $this->dom->createElement(self::ROOT_ELEMENT_NAME);
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
}
