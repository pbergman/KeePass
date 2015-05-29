<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers;

class HeaderException extends \Exception
{
    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function fileSignatureMisMatch($sig1)
    {
        throw new self(sprintf('File signature (sig1) did not match (0X%08x != 0X%08x)', $sig1, AbstractHeader::SIG_1));
    }

    /**
     * @return HeaderException
     * @throws HeaderException
     */
    static function secondFileSignatureMisMatch($sig2, $val1, $val2)
    {
        throw new self(sprintf('Second file signature did not match (0X%08x != 0X%08x or 0X%08x)', $sig2, $val1, $val2));
    }

}