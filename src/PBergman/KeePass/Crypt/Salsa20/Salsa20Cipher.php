<?php
 /**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace PBergman\KeePass\Crypt\Salsa20;

use PBergman\KeePass\Crypt\Salsa20\Core;
use PBergman\KeePass\Stream\StreamWrapper;

/**
 * Class Salsa20Cipher
 *
 * main class, will load core 16 or 32 bit and
 * check the given arguments is the are valid
 *
 * @package PBergman\KeePass\Crypt\Salsa20
 */
class Salsa20Cipher
{
    const CORE_16 = 1;
    const CORE_32 = 2;
    /** @var Core\AbstractSalsa20Core */
    protected $core;
    /** @var  int */
    protected $iv;
    /** @var  int */
    protected $key;
    /** @var  int */
    protected $rounds;
    /** @var StreamWrapper  */
    protected $stream;

    function __construct($key, $iv, $rounds = 20, $core = self::CORE_32)
    {
        if (strlen($iv) !== 8) {
            throw Salsa20CipherException::invalidIVSize();
        }

        if (!in_array($rounds, [8, 12, 20])) {
            throw Salsa20CipherException::invalidRoundLength();
        }

        if (!in_array(strlen($key), [16, 32])) {
            throw Salsa20CipherException::invalidKeySize();
        }

        $this->stream = new StreamWrapper(fopen('php://memory', 'r+b'));
        $this->rounds = $rounds;
        $this->key = $key;
        $this->iv = $iv;

        switch ($core) {
            case self::CORE_32:
                $this->core = new Core\Salsa20Core32(
                    $this->key,
                    $this->iv,
                    $this->rounds,
                    $this->stream
                );
                break;
            case self::CORE_16:
                $this->core = new Core\Salsa20Core16(
                    $this->key,
                    $this->iv,
                    $this->rounds,
                    $this->stream
                );
                break;
            default:
                throw Salsa20CipherException::invalidCoreType();
                break;
        }
    }

    public function reset()
    {
        $this->core = new $this->core(
            $this->key,
            $this->iv,
            $this->rounds,
            $this->stream
        );
    }

    /**
     * @param   $s
     * @return int
     */
    public function decrypt($s)
    {
        return $s ^  $this->core->getNextBytes(strlen($s));
    }

    /**
     * @param   $s
     * @return int
     */
    public function encrypt($s)
    {
        return $s ^  $this->core->getNextBytes(strlen($s));
    }

}