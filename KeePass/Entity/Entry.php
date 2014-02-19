<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

namespace KeePass\Entity;

/**
 * Class    Entry
 * @package KeePass\Entity
 */
class Entry extends BaseEntity
{
    /** @var array */
    private $data;
    /** @var string */
    private $group;

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * will return all data stored or if name is given only the data
     * corresponding the name key if not exist will return null
     *
     * @param  null $key
     * @return bool
     */
    public function getData($key = null)
    {
        $ret = null;

        if ( !is_null($key) ) {
            if ( $this->hasData($key) === true ) {
                $ret = $this->data[$key];
            }
        } else {
            $ret = $this->data;
        }

        return $ret;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function hasData($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

}
