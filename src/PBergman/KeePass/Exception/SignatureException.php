<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Exception;

class SignatureException extends KeepassException
{
    /**
     * SignatureException constructor.
     *
     * @param string $name
     * @param string $a
     * @param string $b
     */
    public function __construct($name, $a, $b)
    {
        parent::__construct(
            sprintf(
                'signature did not match for %s (0X%s != 0X%s)',
                $name,
                bin2hex($a),
                bin2hex($b)
            )
        );
    }
}
