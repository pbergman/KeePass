<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Crypt\Salsa20\Core;

use PBergman\KeePass\Crypt\Salsa20\Salsa20CipherException;
use PBergman\KeePass\Stream\StreamWrapper;

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
    /** @var StreamWrapper  */
    protected $stream;

    /**
     * @param   string $key
     * @param   string $iv
     * @param   int $rounds
     * @param   StreamWrapper $stream
     *
     * @throws  Salsa20CipherException
     */
    abstract public function __construct($key, $iv, $rounds = 20, StreamWrapper $stream);

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
        $loops = $this->getLoops($size);
        foreach ($loops as $length) {
            $this->fillStream();
            $bytes .= $this->stream->read($length);
        }
        $this->stream->seek(0, SEEK_END);
        return $bytes;
    }

    /**
     * will return array stack with sizes, the last one
     * has the remanding of the division. So is size = 130
     * it would return:
     *
     * array(
     *    [0] => (int) 64
     *    [1] => (int) 64
     *    [2] => (int) 2
     * )
     *
     * @param   int   $size
     * @param   int   $loopSize
     * @return  array
     */
    protected function getLoops($size, $loopSize = 64)
    {
        return array_map(function($c) { return array_sum($c); }, array_chunk(array_fill(0, $size, 1), $loopSize));
    }
}
