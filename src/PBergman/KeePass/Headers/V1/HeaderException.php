<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers\V1;

/**
 * Class HeaderException
 *
 * @package PBergman\KeePass\Headers\V1
 */
class HeaderException extends \Exception
{
    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function invalidFileSize($size)
    {
        throw new self(sprintf('File was smaller than db header (%s < %s)', $size, Header::HEADER_SIZE));
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function unsupportedVersion($version)
    {
        throw new self(sprintf('Unsupported file version1 (0X%08x).', $version));
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function UnknownEncryptionType()
    {
        throw new self("Unknown encryption type");
    }

}