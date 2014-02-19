<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace KeePass\EntityController\Filters;

use KeePass\EntityController\Controller;

/**
 * Class    Entry
 * @package KeePass\EntityController\Filters
 */
class Entry extends Filter
{

    /**
     * search a match in the result data fields from earlier where statement
     *
     * @param string $name             field name to search
     * @param string $value            text to match for given data field
     * @param string $comparisonMethod method of comparison: = != < <= => > like
     * @param bool   $caseInsensitive  set true to search case insensitive
     *
     * @return $this
     */
    public function andWhereInData($name, $value, $comparisonMethod, $caseInsensitive)
    {
        $entities       = $this->entities;

        $this->entities = $this->result;

        $this->whereInData($name, $value, $comparisonMethod, $caseInsensitive);

        $this->entities = $entities;

        return $this;
    }

    /**
     * search a or match in the data fields
     *
     * @param string $name             field name to search
     * @param string $value            text to match for given data field
     * @param string $comparisonMethod method of comparison: = != < <= => > like
     * @param bool   $caseInsensitive  set true to search case insensitive
     *
     * @return $this
     */
    public function orWhereInData($name, $value, $comparisonMethod = '=', $caseInsensitive = false)
    {
        $result = $this->result;

        $this->whereInData($name, $value, $comparisonMethod, $caseInsensitive);

        $this->result = array_merge($result, $this->result);

        return $this;
    }

    /**
     * search a match in the data fields
     *
     * @param string $name             field name to search
     * @param string $value            text to match for given data field
     * @param string $comparisonMethod method of comparison: = != < <= => > like
     * @param bool   $caseInsensitive  set true to search case insensitive
     *
     * @return $this
     */
    public function whereInData($name, $value, $comparisonMethod = '=', $caseInsensitive = false)
    {

        $this->result = array();

        foreach ( array_keys($this->entities) as $id ) {

            /** @var $entity \KeePass\Entity\Entry */
            $entity     = $this->shm->varGet($id);
            $data       = $entity->getData();

            if (isset($data[$name])) {

                if (false !== $this->compare($value, $data[$name], $comparisonMethod, $caseInsensitive)) {
                    $this->result[] = $id;
                }

            }

        }

        return $this;

    }

    /**
     * will get group(s) from entries in result
     *
     * @return \KeePass\Entity\Entry|Group
     */
    public function getGroup()
    {
        $result = array();

        foreach ($this->result as $id) {

            /** @var $entry \KeePass\Entity\Entry */
            $entry    = $this->shm->varGet($id);
            $group    = $entry->getGroup();

            if (!isset($result[$group])) {
                $result[$group] = $group;
            }
        }
        $result     = array_values($result);
        $controller = new Controller();
        $controller->setShm($this->shm);
        /** @var Group $entry */
        $entry = $controller->getEntities('group');
        $entry->setEntities(array_flip($result));
        $entry->setResult($result);

        return $entry;
    }

}
