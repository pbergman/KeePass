<?php
 /**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace PBergman\KeePass\Crypt\Salsa20;

/**
 * Class Salsa20CipherException
 *
 * @package PBergman\KeePass\Crypt\Salsa20
 */
class Salsa20CipherException extends \Exception
{
    /**
     * @return Salsa20CipherException
     * @throws Salsa20CipherException
     */
    static function invalidIVSize()
    {
        throw new self('Salsa20 IV length must be 8');
    }

    /**
     * @return Salsa20CipherException
     * @throws Salsa20CipherException
     */
    static function invalidKeySize()
    {
        throw new self('Salsa20 key length must be 16 or 32');
    }

    /**
     * @return Salsa20CipherException
     * @throws Salsa20CipherException
     */
    static function invalidRoundLength()
    {
        throw new self('Salsa20 rounds must be 8, 12, or 20');
    }

    /**
     * @return Salsa20CipherException
     * @throws Salsa20CipherException
     */
    static function invalidCoreType()
    {
        throw new self('Invalid core type supported, 16bit and 32bit only supported');
    }

}