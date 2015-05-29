<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Headers;

use PBergman\KeePass\Headers\V1\Header as HeaderV1;
use PBergman\KeePass\Headers\V2\Header as HeaderV2;
use PBergman\KeePass\Streams\AbstractStreamWrapper;

/**
 * Class Header
 *
 * @package PBergman\KeePass\Headers
 */
class Header
{
    /**
     * Parse header of database
     *
     * @param   AbstractStreamWrapper $buffer
     * @throws  HeaderException
     * @return  null|HeaderV2|HeaderV1
     */
    static function parseHeader(AbstractStreamWrapper $buffer)
    {
        $sig = unpack('L2',  $buffer->read(8));
        $header = null;
        if ($sig[1] !== AbstractHeader::SIG_1) {
            throw HeaderException::fileSignatureMisMatch($sig[1]);
        }
        switch (true) {
            case $sig[2] === HeaderV1::SIG_2:
                // not supported/tested
                $header = new HeaderV1();
                $header->read($buffer);
                return null;
                break;
            case $sig[2] === HeaderV2::SIG_2:
                $header = new HeaderV2();
                $header->read($buffer);
                break;
            default:
                throw HeaderException::secondFileSignatureMisMatch($sig[2], HeaderV1::SIG_2, HeaderV2::SIG_2);
                break;
        }

        return $header;
    }
}