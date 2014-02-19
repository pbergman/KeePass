<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

namespace KeePass\Exceptions;

/**
 * Class    InvalidComparisonOperatorException
 * @package KeePass\Exceptions
 */
class InvalidComparisonOperatorException extends \Exception
{
    public function __construct($option, array $allowed, $code = 0, \Exception $previous = null)
    {
        $message = sprintf(
            'Comparison operator "%s" not recognized, valid comparison operators: [%s]',
            $option,
            implode(', ',$allowed)
        );

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
