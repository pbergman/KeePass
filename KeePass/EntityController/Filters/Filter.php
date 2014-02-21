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
    protected $operators = array('=', '!=', '<', '<=', '<>', '>', '>=', 'like');
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
     * @param  string   $name             the key name of the object witch has to have to match $value
     * @param  string   $value            the value that has to match
     * @param  string   $comparisonMethod the method of comparison
     * @param  bool     $caseInsensitive  set to true when needed to check case insensitive
     *     *
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

        foreach ( array_keys($this->entities) as $id ) {

            /** @var $entity \KeePass\Entity\BaseEntity */
            $entity     = $this->shm->varGet($id);

            if ($entity) {

                $methodName = sprintf('get%s', implode('', array_map('ucfirst', explode('_', $name))));

                if (method_exists($entity, $methodName)) {

                    $result = call_user_func(array($entity,$methodName));

                    if (false !== $this->compare($value, $result, $comparisonMethod, $caseInsensitive)) {
                        $this->result[] = $id;
                    }

                } else {
                    throw new EntityUnknownPropertyException($name, $entity);
                }

            }
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
        }

        return (bool) $return;

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
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
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
     */
    public function setShm(\SharedMemory\Controller $shm)
    {
        $this->shm = $shm;
    }
}
