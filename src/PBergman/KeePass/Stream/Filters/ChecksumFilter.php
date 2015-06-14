<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream\Filters;


use PBergman\KeePass\KeePassException;
use PBergman\KeePass\Stream\StreamWrapper;

class ChecksumFilter extends \php_user_filter
{
//    protected $mode;
//    protected $buffer;
//
//
//
//    const MODE_UNPACK = 1;
//    const MODE_PACK = 2;

//    function onCreate()
//    {
//        switch ($this->filtername) {
//            case 'checksum.unpack':
//                $this->mode = self::MODE_UNPACK;
//                break;
//            default:
//                return false;
//        }
//
////        $this->buffer = fopen('php://memory', 'r+b');
//
//        return true;
//    }

    function onCreate( ) {
        $this->data = '';
        return true;
    }

    function filter($in, $out, &$consumed, $closing) {

        $data = '';

        while ($bucket = stream_bucket_make_writeable($in)) {
            $data .= $bucket->data;
            $consumed += $bucket->datalen;
        }

        throw new \RuntimeException(strlen($data));


//        while ($bucket = stream_bucket_make_writeable($in)) {
//            $this->data .= $bucket->data;
//            $this->bucket = $bucket;
//            $consumed = 0;
//        }
//
//        if ($closing) {
//            $consumed += strlen($this->data);
//            // decode named entities
//            throw new \RuntimeException(strlen($this->data));
//            $this->data = html_entity_decode($this->data);
//            $this->bucket->data = $this->data;
//            $this->bucket->datalen = strlen($this->data);
//            stream_bucket_append($out, $this->bucket);
//            return PSFS_PASS_ON;
//        }

        return PSFS_FEED_ME;
    }
}