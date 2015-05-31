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
 * @method  string      setValue
 * @method  string      getValue
 * @method  string      setKey
 * @method  string      getKey
 */
class String
{
    /** @var \SimpleXMLElement */
    protected $element;

    function __construct(\SimpleXMLElement $element = null)
    {
        if (is_null($element)) {
            $this->element = new \SimpleXMLElement($this->getDefault());
        } else {
            $this->element = $element;
        }
    }

    public function getDefault()
    {
        return <<<EOL
    <String>
        <Key />
        <Value />
    </String>
EOL;
    }


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
     * @return \SimpleXMLElement
     */
    public function getElement()
    {
        return $this->element;
    }
}