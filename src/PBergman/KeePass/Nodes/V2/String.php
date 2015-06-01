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
 * @method  $this       setValue
 * @method  $this       setKey
 *
 * @method  string      getValue
 * @method  string      getKey
 */
class String extends AbstractNode
{
    /**
     * @param   string      $name
     * @param   array       $arguments
     * @return $this|string
     */
    public function __call($name, $arguments)
    {
        if (preg_match('#^(?P<method>get|set)(?P<name>Key|Value)$#', $name, $ret)) {
            switch ($ret['method']) {
                case 'get':
                    return (string) $this->element->$ret['name'];
                    break;
                case 'set':
                    $this->element->$ret['name'] = $arguments[0];
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
     * @return \SimpleXMLElement
     */
    protected function getDefaultElement()
    {
        return new \SimpleXMLElement('<String><Key /><Value /></String>');
    }
}