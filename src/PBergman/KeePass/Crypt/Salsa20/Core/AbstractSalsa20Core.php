<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Crypt\Salsa20\Core;

use PBergman\KeePass\Crypt\Salsa20\Salsa20CipherException;
use PBergman\KeePass\Streams\AbstractStreamWrapper;

/**
 * Class Salsa20
 *
 * base class for salsa20 core
 *
 * @package PBergman\KeePass
 */
abstract class AbstractSalsa20Core
{
    /** @var int  */
    protected $rounds;
    /** @var \SplFixedArray  */
    protected $state;
    /** @var AbstractStreamWrapper  */
    protected $stream;

    /**
     * @param   string $key
     * @param   string $iv
     * @param   int $rounds
     * @param   AbstractStreamWrapper $stream
     *
     * @throws  Salsa20CipherException
     */
    abstract public function __construct($key, $iv, $rounds = 20, AbstractStreamWrapper $stream);

    /**
     * Main core function
     */
    abstract protected function fillStream();

    /**
     * read the X bytes from cypher, that are given with $size
     *
     * @param   int $size
     * @return  null|string
     */
    public function getNextBytes($size)
    {
        $bytes = null;
        while($size) {
            if (0 === ($this->stream->getPos() % 64)) {
                $this->fillStream();
            }
            $length = min(64 - $this->stream->getPos(), $size);
            $bytes .= $this->stream->read($length);
            $size -= $length;
        }
        return $bytes;
    }
}
