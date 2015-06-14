<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Crypt\Salsa20\Core;

use PBergman\KeePass\Crypt\Salsa20\Salsa20CipherException;
use PBergman\KeePass\Stream\StreamWrapper;

/**
 * Class Salsa20Core16
 *
 * Implementation of the salsa20 adapted from C# and PERL,
 *
 * uses the php 16-bits instead of 32-bits, its a bit heavy
 * but should work if you got problems with the 32-bit version.
 *
 * @package PBergman\KeePass\Crypt\Salsa20\Core
 */
class Salsa20Core16 extends AbstractSalsa20Core
{
    /**
     * @param string $key
     * @param string $iv
     * @param int $rounds
     * @param StreamWrapper $stream
     *
     * @throws Salsa20CipherException
     */
    public function __construct($key, $iv, $rounds = 20, StreamWrapper $stream)
    {
        $iv = $iv = array_values(unpack('C8', $iv));
        $this->rounds = $rounds;

        if (strlen($key) === 32) {
            $key = array_values(unpack('C32', $key));
            $control = [0x7865, 0x6170, 0x646e, 0x3320, 0x2d32, 0x7962, 0x6574, 0x6b20]; // SIGMA
        } else {
            $key = array_values(unpack('C32', $key.$key));
            $control = [0x7865, 0x6170, 0x646e, 0x3320, 0x2d36, 0x7962, 0x6574, 0x6b20]; // TAU
        }

        $this->stream = $stream;
        $this->state = new \SplFixedArray(32);
        $this->state[0]  = $control[0];
        $this->state[1]  = $control[1];
        $this->state[2]  = $key[0]  + ($key[1]  << 8);
        $this->state[3]  = $key[2]  + ($key[3]  << 8);
        $this->state[4]  = $key[4]  + ($key[5]  << 8);
        $this->state[5]  = $key[6]  + ($key[7]  << 8);
        $this->state[6]  = $key[8]  + ($key[9]  << 8);
        $this->state[7]  = $key[10] + ($key[11] << 8);
        $this->state[8]  = $key[12] + ($key[13] << 8);
        $this->state[9]  = $key[14] + ($key[15] << 8);
        $this->state[10] = $control[2];
        $this->state[11] = $control[3];
        $this->state[12] = $iv[0] + ($iv[1] << 8);
        $this->state[13] = $iv[2] + ($iv[3] << 8);
        $this->state[14] = $iv[4] + ($iv[5] << 8);
        $this->state[15] = $iv[6] + ($iv[7] << 8);
        $this->state[16] = 0;
        $this->state[17] = 0;
        $this->state[18] = 0;
        $this->state[19] = 0;
        $this->state[20] = $control[4];
        $this->state[21] = $control[5];
        $this->state[22] = $key[16] + ($key[17] << 8);
        $this->state[23] = $key[18] + ($key[19] << 8);
        $this->state[24] = $key[20] + ($key[21] << 8);
        $this->state[25] = $key[22] + ($key[23] << 8);
        $this->state[26] = $key[24] + ($key[25] << 8);
        $this->state[27] = $key[26] + ($key[27] << 8);
        $this->state[28] = $key[28] + ($key[29] << 8);
        $this->state[29] = $key[30] + ($key[31] << 8);
        $this->state[30] = $control[6];
        $this->state[31] = $control[7];
        unset($control, $key, $iv);
    }

    /**
     * Rotates an unsigned 16-bit value to the
     * left by the specified number of bits.
     *
     * @param array $x  object with bits
     * @param array $a  array of keys to add
     * @param int   $r  int representing rotate
     * @param int   $i  original target key
     */
    protected function rotl16(array &$x, array $a, $r, $i)
    {
        $a = [($a[0] * 2), ($a[1] * 2)];
        // Add
        $s = $x[$a[0]] + $x[$a[1]];
        $l = $s >> 16;
        $s = $s & 0xffff;
        $t = ($x[$a[0] + 1] + $x[$a[1]+ 1] + $l) & 0xffff;
        // RotateXor
        $m = $r < 16 ? 0 : 1;
        $r = $r % 16;
        $nt = (($t << $r) & 0xffff) | ($s >> (16 - $r));
        $ns = (($s << $r) & 0xffff) | ($t >> (16 - $r));
        $i = [($i * 2 + $m), ($i * 2 + 1 - $m)];
        $x[$i[0]] = $x[$i[0]] ^ $ns;
        $x[$i[1]] = $x[$i[1]] ^ $nt;
    }

    /**
     * Main core function
     */
    protected function fillStream()
    {
        $x = $this->state->toArray();

        for ($i = 0; $i < $this->rounds/2; $i++) {
            $this->rotl16($x, [0,  12],  7,  4);
            $this->rotl16($x, [4,   0],  9,  8);
            $this->rotl16($x, [8,   4], 13, 12);
            $this->rotl16($x, [12,  8], 18,  0);
            $this->rotl16($x, [5,   1],  7,  9);
            $this->rotl16($x, [9,   5],  9, 13);
            $this->rotl16($x, [13,  9], 13,  1);
            $this->rotl16($x, [1,  13], 18,  5);
            $this->rotl16($x, [10,  6],  7, 14);
            $this->rotl16($x, [14, 10],  9,  2);
            $this->rotl16($x, [2,  14], 13,  6);
            $this->rotl16($x, [6,   2], 18, 10);
            $this->rotl16($x, [15, 11],  7,  3);
            $this->rotl16($x, [3,  15],  9,  7);
            $this->rotl16($x, [7,   3], 13, 11);
            $this->rotl16($x, [11,  7], 18, 15);
            $this->rotl16($x, [0,   3],  7,  1);
            $this->rotl16($x, [1,   0],  9,  2);
            $this->rotl16($x, [2,   1], 13,  3);
            $this->rotl16($x, [3,   2], 18,  0);
            $this->rotl16($x, [5,   4],  7,  6);
            $this->rotl16($x, [6,   5],  9,  7);
            $this->rotl16($x, [7,   6], 13,  4);
            $this->rotl16($x, [4,   7], 18,  5);
            $this->rotl16($x, [10,  9],  7, 11);
            $this->rotl16($x, [11, 10],  9,  8);
            $this->rotl16($x, [8,  11], 13,  9);
            $this->rotl16($x, [9,   8], 18, 10);
            $this->rotl16($x, [15, 14],  7, 12);
            $this->rotl16($x, [12, 15],  9, 13);
            $this->rotl16($x, [13, 12], 13, 14);
            $this->rotl16($x, [14, 13], 18, 15);
        }

        for ($i = 0; $i < count($this->state); $i++) {
            $value = $x[$i] + $this->state[$i];
            $x[$i] = $value & 0xffff; $i++;
            $x[$i] = ($x[$i] + $this->state[$i] + ($value >> 16)) & 0xffff;
        }

        $args[] = 'C*';

        array_walk($x, function($v) use (&$args){
            $args[] = $v & 0xff;
            $args[] = $v >> 8;
        });

        $this->stream->rewrite(call_user_func_array('pack', $args));

        $this->state[16] += 1;

        if ($this->state[16] == 0xffff) {

            $this->state[16] = 0;
            $this->state[17] += 1;

            if ($this->state[17] == 0xffff) {

                $this->state[17] = 0;
                $this->state[18] += 1;

                if ($this->state[18] == 0xffff) {
                    $this->state[18] = 0;
                    $this->state[19] += 1;
                }
            }
        }
    }
}