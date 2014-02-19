<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

namespace KeePass\Entity;

/**
 * Class    Group
 * @package KeePass\Entity
 */
Class Group extends BaseEntity
{
    /** @var array */
    private $groups = array();
    /** @var array */
    private $entries = array();

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param array $entries
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * @param $entry
     */
    public function addEntry($entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @param $group
     */
    public function addGroup($group)
    {
        $this->groups[] = $group;
    }

}
