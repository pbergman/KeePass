<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace KeePass\Exceptions;

/**
 * Class    KeePassScriptErrorException
 * @package KeePass\Exceptions
 */
class KeePassScriptErrorException extends \Exception
{

    public function __construct(\Symfony\Component\Process\Process $cmd, $code = 0, \Exception $previous = null)
    {
        if (!is_null($cmd->getErrorOutput())) {
            $message = $cmd->getErrorOutput();
        } else {
            $message = $cmd->getOutput();
        }

        parent::__construct($message, $cmd->getExitCode(), $previous);
    }

    public function __toString()
    {
        return sprintf(
            "%s:[%s] %s\n",
            __CLASS__,
            $this->getCode(),
            $this->message
        );
    }

}
