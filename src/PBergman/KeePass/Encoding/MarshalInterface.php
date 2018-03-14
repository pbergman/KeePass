<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Encoding;

interface MarshalInterface
{
    /**
     * this method will encode the given value to a
     * string that can be used as a xml value
     *
     * @param mixed $data
     * @return string
     */
    public static function marshal($data);

    /**
     * this method will decode a string xml value to
     * a php value type
     *
     * @param string $data
     * @return mixed
     */
    public static function unmarshal($data);
}