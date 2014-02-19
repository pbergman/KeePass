<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace KeePass\EntityController;

use \SharedMemory\Controller as SHMController;
use \KeePass\Entity\Entry;
use \KeePass\Entity\Group;

/**
 * Class    Controller
 * @package KeePass\EntityController
 */
class Controller
{
    /** @var array  */
    private $groups;
    /** @var array  */
    private $entries;
    /** @var SHMController */
    private $shm;

    /**
     * @param \SharedMemory\Controller $shm
     */
    public function setShm(\SharedMemory\Controller $shm)
    {
        $this->shm = $shm;
    }

    /**
     * @return \SharedMemory\Controller
     */
    public function getShm()
    {
        return $this->shm;
    }

    /** @var \KeePass\Application */
    private $application;

    /**
     * will set application container
     *
     * @param \KeePass\Application $application
     */
    public function setApplication(\KeePass\Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return \KeePass\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * will set/get the group/entry index from the shared memory
     */
    public function initialize()
    {

        if ( false === $this->groups = $this->shm->varGet('group_index') ) {
            $this->groups = array();
        }

        if ( false === $this->entries = $this->shm->varGet('entry_index') ) {
            $this->entries = array();
        }
    }

    /**
     * removes shared memory cache
     *
     * @param bool $includeTimeStamp if true will also removes last modified time stamp of file
     * @param bool $removeIndex      if true will remove also shm index
     */
    public function removeCache($includeTimeStamp = true, $removeIndex = false)
    {
        $ids = array_flip(array_merge($this->groups, $this->entries));

        foreach ($ids as $id) {
            $this->shm->varDel($id);
        }

        $this->shm->varDel('group_index');
        $this->shm->varDel('entry_index');

        if ($includeTimeStamp === true) {
            $this->shm->varDel('db_last_Modified');
        }

        if ($removeIndex === true) {
            $this->shm->destroy();
        }

    }

    /**
     * will get new instance of entry or group
     *
     * @param string $name
     *
     * @return bool|Entry|Group
     */
    public function getNew($name)
    {
        $return = false;

        switch ($name) {
            case 'entry':
                $return = new Entry();
                break;
            case 'group':
                $return = new Group();
                break;
        }

        return $return;
    }

    /**
     * save given entity into memory
     *
     * @param $entity Group|Entry
     */
    public function saveEntity($entity)
    {
        $reflection = new \ReflectionClass($entity);

        if ( false !== $reflection->isSubclassOf('KeePass\Entity\BaseEntity') ) {

            $uuid       = $entity->getUuid();
            $namespace  = $entity->getNamespace();

            switch ( $reflection->getShortName() ) {
                case 'Entry':

                    if ( !isset($this->entries[$uuid] )  ) {
                        $this->entries[$uuid] = $namespace;
                        $this->shm->varSet('entry_index', $this->entries);
                    }

                    break;
                case 'Group';

                    if ( !isset($this->groups[$uuid] )  ) {
                        $this->groups[$uuid] = $namespace;
                        $this->shm->varSet('group_index', $this->groups);
                    }

                    break;
            }

            $this->shm->varSet($uuid, $entity);

        }

    }

    /**
     * will give all entities from given group in the filter instance
     *
     * @param $type
     *
     * @return bool|Filters\Entry|Filters\Group
     */
    public function getEntities($type)
    {
        $type   = strtolower($type);
        $return = false;

        switch ($type) {
            case 'entry':
                $return = new Filters\Entry();
                $return->setShm($this->shm);
                $return->setEntities($this->entries);
                break;
            case 'group':
                $return = new Filters\Group();
                $return->setShm($this->shm);
                $return->setEntities($this->groups);
                break;
        }

        return $return;

    }

}
