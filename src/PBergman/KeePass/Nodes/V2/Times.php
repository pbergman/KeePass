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

    function __construct(\SimpleXMLElement $element = null)
    {
        parent::__construct($element);

        if  (!$element) {
            $this
                ->setCreationTime(new \DateTime('now', new \DateTimeZone('Z')))
                ->setLastModificationTime(new \DateTime('now', new \DateTimeZone('Z')))
                ->setLastAccessTime(new \DateTime('now', new \DateTimeZone('Z')))
                ->setExpiryTime(new \DateTime('now', new \DateTimeZone('Z')))
                ->setCreationTime(new \DateTime('now', new \DateTimeZone('Z')))
                ->setLocationChanged(new \DateTime('now', new \DateTimeZone('Z')));
        }


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
                    switch($ret['name']) {
                        case 'CreationTime':
                        case 'LastModificationTime':
                        case 'LastAccessTime':
                        case 'ExpiryTime':
                        case 'LocationChanged':
                            return new \DateTime($this->element->$ret['name']);
                            break;
                        case 'Expires':
                            $this->element->$ret['name'] = (strtolower($arguments[0]) === 'true') ? true : false;
                            break;
                        case 'UsageCount':
                            $this->element->$ret['name'] = $arguments[0];
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
                                $this->element->$ret['name'] = $arguments[0]->format(self::DATE_FORMAT);
                            }
                            break;
                        case 'Expires':
                            $this->element->$ret['name'] = ($arguments[0]) ? 'True' : 'False';
                            break;
                        case 'UsageCount':
                            $this->element->$ret['name'] = $arguments[0];
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));

                    }
                    return $this;
                    break;
            }
        } else {
            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));
        }
    }


    /**
     * returns the default xml that specifies this node
     *
     * @return mixed
     */
    protected function getDefaultElement()
    {
        return new \SimpleXMLElement('
            <Times>
                <CreationTime />
                <LastModificationTime />
                <LastAccessTime />
                <ExpiryTime />
                <Expires>False</Expires>
                <UsageCount>0</UsageCount >
                <LocationChanged />
            </Times>');
    }

}
