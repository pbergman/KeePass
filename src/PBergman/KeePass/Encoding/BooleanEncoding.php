<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Encoding;

use PBergman\KeePass\Exception\InvalidArgumentException;

class BooleanEncoding implements MarshalInterface
{

    /**
     * @inheritdoc
     */
    public static function marshal($data)
    {
        if (!is_bool($data)) {
            throw new InvalidArgumentException('expected a boolean value got ' . gettype($data));
        }

        return (true === $data) ? 'True' : 'False';
    }

    /**
     * @inheritdoc
     */
    public static function unmarshal($data)
    {
        return $data === 'True';
    }
}