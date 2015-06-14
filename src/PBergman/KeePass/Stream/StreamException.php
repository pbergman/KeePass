<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

class StreamException extends \Exception
{
    /**
     * @param   mixed   $argument
     * @throws  StreamException
     * @return  StreamException
     */
    static function argumentNotResource($argument)
    {
        throw new self(sprintf('Expecting a valid resource give: %s', gettype($argument)));
    }

    /**
     * @throws  StreamException
     * @return  StreamException
     */
    static function argumentNotNumeric()
    {
        throw new self('Numeric offset only supported');
    }

    /**
     * @throws  StreamException
     * @return  StreamException
     */
    static function argumentNotString()
    {
        throw new self('Stream can only add content of type "string"');
    }

}