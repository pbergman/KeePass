<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace KeePass\EntityController\Filters;

use KeePass\Exceptions\InvalidComparisonOperatorException;
use KeePass\Exceptions\EntityUnknownPropertyException;

/**
 * Class    Filter
 * @package KeePass\EntityController\Filters
 */
class Filter
{
    /** @var array  */
    protected $entities;
    /** @var array  */
    protected $result    = array();
    /** @var array  */
    protected $operators = array('=', '!=', '<', '<=', '<>', '>', '>=', 'like', 'in');
    /** @var \SharedMemory\Controller */
    protected $shm       = null;

    /**
     * helper for a or where statement, same as where function with $or param set to true
     *
     * @param  string   $name             the key name of the object witch has to have to match $value
     * @param  string   $value            the value that has to match
     * @param  string   $comparisonMethod the method of comparison
     * @param  bool     $caseInsensitive  set to true when needed to check case insensitive
     *
     * @return $this
     */
    public function orWhere($name, $value, $comparisonMethod = '=', $caseInsensitive = false)
    {
        $result = $this->result;

        $this->where($name, $value, $comparisonMethod, $caseInsensitive);

        $this->result = array_merge($result, $this->result);

        return $this;
    }

    /**
     * will save and where
     *
     * @param  string   $name             the key name of the object witch has to have to match $value
     * @param  string   $value            the value that has to match
     * @param  string   $comparisonMethod the method of comparison
     * @param  bool     $caseInsensitive  set to true when needed to check case insensitive
     *
     * @return $this
     */
    public function andWhere($name, $value, $comparisonMethod = '=', $caseInsensitive = false)
    {
        $entities       = $this->entities;

        $this->entities = $this->result;

        $this->where($name, $value, $comparisonMethod, $caseInsensitive);

        $this->entities = $entities;

        return $this;
    }

    /**
     * @param  string           $name             the key name of the object witch has to have to match $value
     * @param  array|string     $value            the value that has to match
     * @param  string           $comparisonMethod the method of comparison
     * @param  bool             $caseInsensitive  set to true when needed to check case insensitive
     *
     * @return $this
     *
     * @throws \KeePass\Exceptions\InvalidComparisonOperatorException
     * @throws \KeePass\Exceptions\EntityUnknownPropertyException
     */
    public function where($name, $value, $comparisonMethod = '=', $caseInsensitive = false)
    {
        $this->result = array();

        if (!in_array($comparisonMethod, $this->operators)) {
            throw new InvalidComparisonOperatorException($comparisonMethod,  $this->operators);
        }

        switch ($name) {
            case 'namespace':
                foreach ( $this->entities as $id => $namespace ) {
                    if (false !== $this->compare($value, $namespace, $comparisonMethod, $caseInsensitive)) {
                        $this->result[] = $id;
                    }
                }
                break;
            case 'uuid':
                foreach ( array_keys($this->entities) as $id ) {
                    if (false !== $this->compare($value, $id, $comparisonMethod, $caseInsensitive)) {
                        $this->result[] = $id;
                    }
                }
                break;
            default:
                foreach ( array_keys($this->entities) as $id ) {

                    /** @var $entity \KeePass\Entity\BaseEntity */
                    $entity     = $this->shm->varGet($id);

                    if ($entity) {

                        if (method_exists($entity, $this->formatGetMethodName($name))) {

                            $result = call_user_func(array($entity, $this->formatGetMethodName($name)));

                            if (false !== $this->compare($value, $result, $comparisonMethod, $caseInsensitive)) {
                                $this->result[] = $id;
                            }

                        } else {
                            throw new EntityUnknownPropertyException($name, $entity);
                        }

                    }

                }
                break;
        }

        return $this;

    }

    /**
     * helper function to compare to given values
     *
     *
     * @param string $a  input value that needs to be compared to $b (given answer)
     * @param string $b  the value that $a is going to be compared with ( the answer )
     * @param string $cm sets the comparison method
     * @param bool   $ci set check case insensitive
     *
     * @return bool
     *
     * @throws \KeePass\Exceptions\InvalidComparisonOperatorException
     * @throws \InvalidArgumentException
     */
    protected function compare($a, $b, $cm = '=', $ci = false)
    {

        if (!in_array($cm, $this->operators)) {
            throw new InvalidComparisonOperatorException($cm,  $this->operators);
        }

        $return = false;

        switch ($cm) {
            case '=':
                $return =  ($ci) ? (strtolower($a) == strtolower($b)) : ($a == $b);
                break;
            case 'like':

                $replace = array(
                    '/^(?!\%)/'  => '^',
                    '/^(\%)/'    => '',
                    '/(?<!\%)$/' => '$',
                    '/\%$/'      => '',
                );

                $pattern = preg_replace(array_keys($replace), array_values($replace), $a);
                $pattern = ($ci) ? sprintf('/%s/i', $pattern) : sprintf('/%s/', $pattern) ;
                $return  = (preg_match($pattern, $b) > 0);

                break;
            case '!=':
            case '<>':
                $return = ($ci) ? (strtolower($a) != strtolower($b)) : ($a != $b);
                break;
            case '>':
                $return = ($a > $b);
                break;
            case '>=':
                $return = ($a >= $b);
                break;
            case '<':
                $return = ($a < $b);
                break;
            case '<=':
                $return = ($a <= $b);
                break;
            case 'in':
                if (!is_array($b)) {
                    throw new \InvalidArgumentException('the data to compare needs to by an array! for comparison method \'in\'');
                } else {
                    return in_array($a, $b);
                }
                break;
        }

        return (bool) $return;

    }

    /**
     * will do a search on all properties from entity
     * will try to match it like 'where like' function
     *
     * @param   string    $search   search pattern, wildcards are % for begin or end
     * @param   bool      $ci       case insensitive
     *
     * @return  $this
     *
     * @throws \Exception
     */
    public function search($search, $ci = false)
    {
        $result = array();

        if (!empty($this->result)) {

            foreach ($this->result as $id) {

                /** @var \KeePass\Entity\BaseEntity $entity */
                $entity     = $this->shm->varGet($id);

                if ($entity) {

                    $entityRef  = new \ReflectionClass($entity);
                    $properties = array_merge($entityRef->getParentClass()->getProperties(), $entityRef->getProperties());

                    foreach ( $properties as $property ){

                        $propertyValue = call_user_func(array($entity, $this->formatGetMethodName($property->name)));

                        switch (gettype($propertyValue)) {
                            case 'array':
                                foreach ( $propertyValue  as $value ){
                                    if ($this->compare($search, $value, 'like', $ci) === true ) {
                                        $result[] = $id;
                                        break 3;
                                    }
                                }
                                break;
                            case 'object':
                                switch (get_class($propertyValue)){
                                    case 'DateTime':
                                        /** @var \DateTime $propertyValue */
                                        if ($this->compare($search, $propertyValue->format('Y-m-d H:i:s'), 'like', $ci) === true ) {
                                                $result[] = $id;
                                                break 3;
                                        }
                                        break;
                                }
                                break;
                            case 'integer':
                            case 'boolean':
                            case 'double':
                                if ($this->compare($search, $propertyValue) === true ) {
                                    $result[] = $id;
                                    break 2;
                                }
                                break;
                            case 'string':
                                if ($this->compare($search, $propertyValue, 'like', $ci) === true ) {
                                    $result[] = $id;
                                    break 2;
                                }
                                break;
                            default:
                                throw new \Exception(sprintf('Unsupported format for comparison: %s', gettype($propertyValue)));
                                break;

                        }

                    }

                }

            }

            $this->result = $result;

        }

        return $this;
    }

    /**
     * sets limit on result
     *
     * @param int $length
     * @param int $offset
     *
     * @return $this
     */
    public function limit($length, $offset = 0)
    {
        $this->result = array_slice($this->result, $offset, $length);

        return $this;
    }

    /**
     * will return the entity results
     *
     * @return array
     */
    public function getResult()
    {
        $return = array();

        if (!empty($this->result)) {

            foreach ($this->result as $id) {
                $return[] = $this->shm->varGet($id);
            }

        }

        return $return;
    }

    /**
     * will return single result
     *
     * @return bool|null|string
     */
    public function getSingleResult()
    {
        $return = null;

        if (!empty($this->result)) {
            $return = $this->shm->varGet(current($this->result));
        }

        return $return;
    }

    /**
     * will set result when going for example from group
     * entity to entry entity with getEntriesFromName
     *
     *
     * @param array $result
     */
    protected function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * will return all the entities that were set
     *
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * entities to search through
     *
     * @param mixed $entities
     *
     * @return $this
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;

        if (!empty($entities)) {
            $this->setResult(array_values(array_flip($entities)));
        }

        return $this;
    }

    /**
     * @return \SharedMemory\Controller
     */
    public function getShm()
    {
        return $this->shm;
    }

    /**
     * @param \SharedMemory\Controller $shm
     *
     * @return $this
     */
    public function setShm(\SharedMemory\Controller $shm)
    {
        $this->shm = $shm;

        return $this;
    }

    /**
     * will return formatted get name
     *
     * @param   string $name   name of property
     * @return  string
     */
    protected function formatGetMethodName($name)
    {
        return sprintf('get%s', implode('', array_map('ucfirst', explode('_', $name))));
    }
}
