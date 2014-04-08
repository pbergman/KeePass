<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Exceptions;

/**
 * Class    EntityUnknownPropertyException
 *
 * @package PBergman\KeePass\Exceptions
 */
class EntityUnknownPropertyException extends \Exception
{
    public function __construct($name, $class, $code = 0, \Exception $previous = null)
    {

        $ref     = new \ReflectionClass($class);
        $message = sprintf('Unknown property "%s" for "%s"', $name, $ref->getName());

        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return sprintf(
            "%s: %s\n",
            __CLASS__,
            $this->message
        );
    }
}
