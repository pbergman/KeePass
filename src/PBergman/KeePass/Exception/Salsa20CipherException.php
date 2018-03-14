<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Exception;

/**
 * Class Salsa20CipherException
 *
 * @package PBergman\KeePass\Exception
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
        throw new self('Salsa20 key length must be 16 or 32 given');
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