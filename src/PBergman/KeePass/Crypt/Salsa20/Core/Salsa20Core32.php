<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Crypt\Salsa20\Core;

use PBergman\KeePass\Crypt\Salsa20\Salsa20CipherException;
use PBergman\KeePass\Streams\AbstractStreamWrapper;

/**
 * Class Salsa20Core32
 *
 * Implementation of the salsa20 adapted from C# and PERL,
 *
 * @package PBergman\KeePass\Crypt\Salsa20\Core
 */
class Salsa20Core32 extends AbstractSalsa20Core
{
    /**
     * @param   string $key
     * @param   string $iv
     * @param   int $rounds
     * @param   AbstractStreamWrapper $stream
     *
     * @throws  Salsa20CipherException
     */
    public function __construct($key, $iv, $rounds = 20, AbstractStreamWrapper $stream)
    {
        $iv = array_values(unpack('L2', $iv));
        $this->rounds = $rounds;

        if (strlen($key) === 32) {
            $key = array_values(unpack('L8', $key));
            $control = [0x61707865, 0x3320646e, 0x79622d32, 0x6b206574]; // SIGMA
        } else {
            $key = array_values(unpack('L8', $key.$key));
            $control = [0x61707865, 0x3120646e, 0x79622d36, 0x6b206574]; // TAU
        }

        $this->stream = $stream;
        $this->state = new \SplFixedArray(16);
        $this->state[0]  = $control[0];
        $this->state[1]  = $key[0];
        $this->state[2]  = $key[1];
        $this->state[3]  = $key[2];
        $this->state[4]  = $key[3];
        $this->state[5]  = $control[1];
        $this->state[6]  = $iv[0];
        $this->state[7]  = $iv[1];
        $this->state[8]  = 0;
        $this->state[9]  = 0;
        $this->state[10] = $control[2];
        $this->state[11] = $key[4];
        $this->state[12] = $key[5];
        $this->state[13] = $key[6];
        $this->state[14] = $key[7];
        $this->state[15] = $control[3];
        unset($control, $key, $iv);
    }

    /**
     * Rotates an unsigned 32-bit value to the
     * left by the specified number of bits.
     *
     * @param   int   $x
     * @param   int   $y
     * @return  int
     */
    public function rotl32($x, $y)
    {
        return (($x << $y) | ($x >> (32 - $y))) & 0xffffffff;
    }

    /**
     * Main core function
     */
    protected function fillStream()
    {
        $x = $this->state->toArray();

        for ($i = 0; $i < $this->rounds/2; $i++) {
            $x[ 4] ^= $this->rotl32(($x[ 0] + $x[12]) & 0xffffffff,  7);
            $x[ 8] ^= $this->rotl32(($x[ 4] + $x[ 0]) & 0xffffffff,  9);
            $x[12] ^= $this->rotl32(($x[ 8] + $x[ 4]) & 0xffffffff, 13);
            $x[ 0] ^= $this->rotl32(($x[12] + $x[ 8]) & 0xffffffff, 18);
            $x[ 9] ^= $this->rotl32(($x[ 5] + $x[ 1]) & 0xffffffff,  7);
            $x[13] ^= $this->rotl32(($x[ 9] + $x[ 5]) & 0xffffffff,  9);
            $x[ 1] ^= $this->rotl32(($x[13] + $x[ 9]) & 0xffffffff, 13);
            $x[ 5] ^= $this->rotl32(($x[ 1] + $x[13]) & 0xffffffff, 18);
            $x[14] ^= $this->rotl32(($x[10] + $x[ 6]) & 0xffffffff,  7);
            $x[ 2] ^= $this->rotl32(($x[14] + $x[10]) & 0xffffffff,  9);
            $x[ 6] ^= $this->rotl32(($x[ 2] + $x[14]) & 0xffffffff, 13);
            $x[10] ^= $this->rotl32(($x[ 6] + $x[ 2]) & 0xffffffff, 18);
            $x[ 3] ^= $this->rotl32(($x[15] + $x[11]) & 0xffffffff,  7);
            $x[ 7] ^= $this->rotl32(($x[ 3] + $x[15]) & 0xffffffff,  9);
            $x[11] ^= $this->rotl32(($x[ 7] + $x[ 3]) & 0xffffffff, 13);
            $x[15] ^= $this->rotl32(($x[11] + $x[ 7]) & 0xffffffff, 18);

            $x[ 1] ^= $this->rotl32(($x[ 0] + $x[ 3]) & 0xffffffff,  7);
            $x[ 2] ^= $this->rotl32(($x[ 1] + $x[ 0]) & 0xffffffff,  9);
            $x[ 3] ^= $this->rotl32(($x[ 2] + $x[ 1]) & 0xffffffff, 13);
            $x[ 0] ^= $this->rotl32(($x[ 3] + $x[ 2]) & 0xffffffff, 18);
            $x[ 6] ^= $this->rotl32(($x[ 5] + $x[ 4]) & 0xffffffff,  7);
            $x[ 7] ^= $this->rotl32(($x[ 6] + $x[ 5]) & 0xffffffff,  9);
            $x[ 4] ^= $this->rotl32(($x[ 7] + $x[ 6]) & 0xffffffff, 13);
            $x[ 5] ^= $this->rotl32(($x[ 4] + $x[ 7]) & 0xffffffff, 18);
            $x[11] ^= $this->rotl32(($x[10] + $x[ 9]) & 0xffffffff,  7);
            $x[ 8] ^= $this->rotl32(($x[11] + $x[10]) & 0xffffffff,  9);
            $x[ 9] ^= $this->rotl32(($x[ 8] + $x[11]) & 0xffffffff, 13);
            $x[10] ^= $this->rotl32(($x[ 9] + $x[ 8]) & 0xffffffff, 18);
            $x[12] ^= $this->rotl32(($x[15] + $x[14]) & 0xffffffff,  7);
            $x[13] ^= $this->rotl32(($x[12] + $x[15]) & 0xffffffff,  9);
            $x[14] ^= $this->rotl32(($x[13] + $x[12]) & 0xffffffff, 13);
            $x[15] ^= $this->rotl32(($x[14] + $x[13]) & 0xffffffff, 18);
        }

        $args[] = 'L16';

        for ($i = 0; $i < count($this->state); $i++) {
            $args[] = ($x[$i] + $this->state[$i]) & 0xffffffff;
        }

        $this->stream->rewrite(call_user_func_array('pack', $args));

        $this->state[8] = ($this->state[8] + 1) & 0xffffffff;

        if ($this->state[8] === 0) {
            $this->state[9] = ($this->state[9] + 1) & 0xffffffff;
        }

    }

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