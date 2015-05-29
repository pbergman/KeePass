<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Headers;

abstract class AbstractHeader extends \SplFixedArray implements HeaderInterface
{
    const SIG_1 = 0x9AA2D903;

    const VERSION = 1;
    const SEED_RAND = 2;
    const SEED_KEY = 3;
    const ROUNDS = 4;
    const ENC_IV = 5;
    const VER = 6;
    const HEADER_SIZE = 7;
    const ENC_TYPE = 8;



    /**
     * @inheritdoc
     */
    public function __construct($size = 9)
    {
        parent::__construct($size);
        foreach ($this->getDefaults() as $k => $v) {
            $this[$k] = $v;
        }
    }

    /**
     * @param   bool        $associative
     * @return  array|void
     */
    public function toArray($associative = true)
    {
        if ($associative) {
            $return = [];
            $constants = $this->getConstants();
            foreach ($this as $k => $v) {
                if (isset($constants[$k])) {
                    $return[$constants[$k]] = $this[$k];
                } else {
                    $return[$k] = $v;
                }
            }
            return $return;
        } else {
            return parent::toArray();
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($index)
    {
        if (is_numeric($index)) {
            return parent::offsetGet($index);
        } else {
            if (!in_array(strtolower($index), $this->getConstants())) {
                throw new \InvalidArgumentException(sprintf('No header property defined by name: "%s"', $index));
            } else {
                return parent::offsetGet(constant( get_class($this) . '::' . strtoupper($index)));
            }
        }
    }

    /**
     * get all constant that represent a key index of array
     *
     * @param   array $exclude
     * @return  array
     */
    protected function getConstants($exclude = ['SIG_1', 'SIG_2', 'DB_VER_DW'])
    {
        $class = new \ReflectionClass($this);
        return array_filter(array_map('strtolower', array_flip($class->getConstants())), function($v) use ($exclude) {
                return !in_array($v, $exclude);
        });
    }

    /**
     * set default for header
     *
     * @return array
     */
    abstract protected function getDefaults();

}