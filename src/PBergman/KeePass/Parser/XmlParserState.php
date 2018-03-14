<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser;

use PBergman\KeePass\Exception\XmlParseException;
use PBergman\KeePass\Parser\Element\NoOpElementParser;

final class XmlParserState
{
    /** able to read/handle more data */
    const STATE_FEED = 0x01;
    /** finished parsing the xml before EOF  */
    const STATE_FINISHED = 0x02;
    /** indicates that the current element should be handled by a different handler */
    const STATE_FORWARD_CHILD = 0x04;

    /**
     * XmlParserState constructor.
     */
    private function __construct()
    {
    }
}