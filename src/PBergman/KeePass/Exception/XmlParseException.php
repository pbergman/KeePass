<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Exception;

class XmlParseException extends KeepassException
{
    /**
     * XmlParseException constructor.
     *
     * @param resource $parser
     */
    public function __construct($parser)
    {
        parent::__construct(
            sprintf(
                "xml error: %s at line %d",
                \xml_error_string(\xml_get_error_code($parser)),
                \xml_get_current_line_number($parser)
            )
        );
    }

}