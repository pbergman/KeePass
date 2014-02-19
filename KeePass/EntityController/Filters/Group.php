<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

namespace KeePass\EntityController\Filters;

use KeePass\EntityController\Controller;
use KeePass\KeePass;

/**
 * Class    Group
 * @package KeePass\EntityController\Filters
 */
class Group extends Filter
{

    /**
     * will get entries from selected groups amd
     * return them in a entry filter instance
     *
     *
     * @return Entry
     */
    public function getEntries()
    {
        $result = array();

        foreach ($this->result as $id) {

            /** @var $group \KeePass\Entity\Group */
            $group  = $this->shm->varGet($id);

            if (!empty($group)) {
                $result = array_merge($result, $group->getEntries());
            }
        }

        $controller = new Controller();
        $controller->setShm($this->shm);
        /** @var Entry $entry */
        $entry = $controller->getEntities('entry');
        $entry->setEntities(array_flip($result));
        $entry->setResult($result);

        return $entry;
    }

    /**
     * will get entries from give group name
     *
     * @param $name
     *
     * @return Entry
     */
    public function getEntriesFromName($name)
    {
        $this->where('name', $name);

        return $this->getEntries();
    }

    /**
     * will get group from selected results
     *
     * @return $this
     */
    public function getGroups()
    {
        $result = array();

        foreach ($this->result as $id) {
            /** @var $group \KeePass\Entity\Group */
            $group  = $this->shm->varGet($id);

            if (!empty($group)) {
                $result = array_merge($result, $group->getGroups());
            }
        }

        $this->setEntities(array_flip($result));
        $this->setResult($result);

        return $this;
    }
}
