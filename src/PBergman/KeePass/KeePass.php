<?php
/**
* @author    Philip Bergman <pbergman@live.nl>
* @copyright Philip Bergman
*/
namespace PBergman\KeePass;

use PBergman\KeePass\Headers\Header;
use PBergman\KeePass\Streams;

class KeePass
{
    /**
     * open a keepass database
     *
     * @param   string  $file
     * @param   string  $password
     * @return  array|null|resource|string
     * @throws  \Exception
     */
    public function loadFile($file, $password)
    {
        $buffer = new Streams\FileStream($file);
        $header = Header::parseHeader($buffer);

        switch ($header[$header::VERSION]) {
            case 1:
                // not supported now
                return null;
                break;
            case 2:
                $key = (string) Key::generate($password, $header);
                $buffer = new Streams\TempStream(mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_128,
                    $key,
                    $buffer->getContent(),
                    MCRYPT_MODE_CBC,
                    $header[$header::ENC_IV]
                ));

                if ($buffer->read(32) !== $header[$header::START_BYTES]) {
                    throw new KeePassException('The database key appears invalid or else the database is corrupt.');
                }

                Checksum::unpack($buffer);

                if ((int) $header[$header::COMPRESSION] === 1) {
                    if (false === $data = gzdecode($buffer->getContent())) {
                        throw new KeePassException('Could not decompress data');
                    } else {
                        $buffer->rewrite($data);
                    }
                }

                return $buffer;
                break;
            default:
                throw new KeePassException(sprintf('Unsupported keepass database version %s', $header[$header::VERSION]));

        }
    }
}
