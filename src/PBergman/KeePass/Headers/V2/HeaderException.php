<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers\V2;

/**
 * Class HeaderException
 *
 * @package PBergman\KeePass\Headers\V2
 */
class HeaderException extends \Exception
{
    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function unsupportedVersion($version)
    {
        throw new self(sprintf('Unsupported file version2 (0X%08x).', $version));
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function cipherNotMatch()
    {
        throw new self('Cipher id did not match AES.');
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function compressionToLarge()
    {
        throw new self('Compression was too large.');
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function invalidLength($name, $max)
    {
        throw new self(sprintf('Length of %s was not %s.', $name, $max));
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function unknownType($type, $value)
    {
        throw new self(sprintf('Found an unknown header type (%s, %s)', $type, $value));
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function notSalsa20()
    {
        throw new self('Inner stream id did not match Salsa20.');
    }
}