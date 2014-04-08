<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\EntityController\Filters;

use PBergman\KeePass\EntityController\Controller;

/**
 * Class    Group
 *
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

            /** @var $group \PBergman\KeePass\Entity\Group */
            $group  = $this->shm->varGet($id);

            if (!empty($group)) {
                $result = array_merge($result, $group->getEntries());
            }
        }

        $controller = new Controller();
        $controller->setShm($this->shm);
        /** @var Entry $entry */
        $entry = $controller->getEntities('entry')
                            ->setEntities($result);
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
            /** @var $group \PBergman\KeePass\Entity\Group */
            $group  = $this->shm->varGet($id);

            if (!empty($group)) {
                $result = array_merge($result, $group->getGroups());
            }
        }

        $this->setEntities($result);

        return $this;
    }
}
