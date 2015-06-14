<?php
/**
* @author    Philip Bergman <pbergman@live.nl>
* @copyright Philip Bergman
*/
namespace PBergman\KeePass;

use PBergman\KeePass\Headers\Header;
use PBergman\KeePass\Nodes\V2\Node;
use PBergman\KeePass\Stream\StreamWrapper;

class KeePass
{
    const STREAM_IV = "\xe8\x30\x09\x4b\x97\x20\x5d\x2a";

    /**
     * open a keepass database
     *
     * @param   string  $file
     * @param   string  $password
     * @return  null|Node
     * @throws  \Exception
     */
    public function loadFile($file, $password)
    {
        $buffer = new StreamWrapper(fopen($file, 'rb'));
        $header = Header::parseStream($buffer);

        switch ($header[$header::VERSION]) {
            case 1:
                // not supported now
                return null;
                break;
            case 2:
                $key = (string) Key::generate($password, $header);

                $filter = $buffer->appendFilter(sprintf('mdecrypt.%s', MCRYPT_RIJNDAEL_128), STREAM_FILTER_READ, [
                    'iv'    => $header[$header::ENC_IV],
                    'key'   => $key,
                    'mode'  => MCRYPT_MODE_CBC
                ]);
//
////                $content = $buffer->getContent();
////                $buffer = new StreamWrapper(fopen('php://temp', 'w+b'));
//
//                $buffer->write(
//                    mcrypt_decrypt(
//                        MCRYPT_RIJNDAEL_128,
//                        $key,
//                        $content,
//                        MCRYPT_MODE_CBC,
//                        $header[$header::ENC_IV]
//                    )
//                );
//
//                $buffer->rewind();
//
//                $content = $buffer->getContent();
//
//                $r = substr($content, 0, 32);
//
//                var_dump(bin2hex($r), bin2hex($header[$header::START_BYTES]));exit;

                if ($buffer->read(32) !== $header[$header::START_BYTES]) {
                    throw new KeePassException('The database key appears invalid or else the database is corrupt.');
                }

                var_dump($buffer->getPos(), $buffer->bytesLeft(), $buffer->getInfo()['size']);

                $buffer->setHandler(fopen('php://memory', 'r+b'));

                var_dump($buffer->getPos(), $buffer->bytesLeft(), $buffer->getInfo()['size']);exit;
//                $buffer->removeFilter($filter);

//                $ret = Checksum::unpack($buffer, $filter);

                $ret = Checksum::unpack($buffer);

//                $buffer->removeFilter($filter);
//                $buffer->registerFilter('checksum.*',  'PBergman\KeePass\Stream\Filters\ChecksumFilter');
//                $buffer->appendFilter('checksum.unpack', STREAM_FILTER_READ);

//                $filter = $buffer->appendFilter('checksum.unpack', STREAM_FILTER_READ);

                var_dump(gzdecode($buffer->getContent()));exit;


                if ((int) $header[$header::COMPRESSION] === 1) {

                    if (false === $ret = gzdecode($buffer->getContent())) {
                        throw new KeePassException('Could not decompress data');
                    }
//                    else {
//                        $buffer->rewrite($data);
//                    }
                }

                return new Node($ret, $header);
                break;
            default:
                throw new KeePassException(sprintf('Unsupported keepass database version %s', $header[$header::VERSION]));

        }
    }
}
