<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Encoding;

use PBergman\KeePass\Exception\InvalidArgumentException;

class DateTimeEncoding implements MarshalInterface
{

    /**
     * @inheritdoc
     */
    public static function marshal($data)
    {
        if (!$data instanceof  \DateTimeInterface) {
            throw new InvalidArgumentException('expected a DateTimeInterface value got ' . is_object($data) ? get_class($data) : gettype($data));
        }

        return $data->format(\DateTime::ISO8601);
    }

    /**
     * @inheritdoc
     */
    public static function unmarshal($data)
    {
        return \DateTimeImmutable::createFromFormat(\DateTime::ISO8601, $data);
    }
}