<?php
 /**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\KeePass;

use PBergman\KeePass\Headers\AbstractHeader;
use PBergman\KeePass\Headers\V1\Header as HeaderV1;
use PBergman\KeePass\Headers\V2\Header as HeaderV2;

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
        $buffer = fopen($file, 'rb');
        $header = $this->parseHeader($buffer);
        rewind($buffer);
        fseek($buffer, $header[$header::HEADER_SIZE]);

        switch ($header[$header::VERSION]) {
            case 1:
                // not supported now
                return null;
                break;
            case 2:
                $key = (string) Key::getMasterKeyByHeader($password, $header);//$this->getMasterKey($password, null, $header);
                $stats = fstat($buffer);
                $body = \mcrypt_decrypt(        // php5-mcrypt
                    MCRYPT_RIJNDAEL_128,
                    $key,
                    fread($buffer, $stats['size']),
                    MCRYPT_MODE_CBC,
                    $header[$header::ENC_IV]
                );

                $buffer = fopen('php://temp', 'wb');
                fwrite($buffer, $body, strlen($body));
                rewind($buffer);
                $stats = fstat($buffer);
                unset($body);

                if (fread($buffer, 32) !== $header[$header::START_BYTES]) {
                    throw new \RuntimeException('The database key appears invalid or else the database is corrupt.');
                }

                $buffer = $this->unchecksum(fread($buffer, $stats['size']));

                if ((int) $header[$header::COMPRESSION] === 1) {
                    $buffer = gzdecode($buffer);
                }

                return $buffer;
                break;
            default:
                throw new \RuntimeException(sprintf('Unsupported keepass database version %s', $header[$header::VERSION]));

        }
    }

    /**
     * validate the data
     *
     * @param   $data
     * @return  array|string
     * @throws \Exception
     */
    function unchecksum($data)
    {
        $ret = '';
        $pos = 0;
        while ($pos < strlen($data)) {
            $bin = unpack(sprintf('@%d/Lindex/a32hash/isize', $pos), $data);
            $pos += 40;
            if ($bin['size'] === 0) {
                if ($bin['hash'] !== str_repeat(chr(0), 32)) {
                    throw new \Exception(sprintf('Found mismatch for 0 chunksize, 0x32 != %s', dechex($bin['hash'])));
                }
                break;
            }
            $chunk = substr($data, $pos, $bin['size']);
            if ($bin['hash'] !== hash('sha256', $chunk, true)) {
                throw new \RuntimeException(sprintf(
                    'Chunk hash of index %s did not match, %s != %s',
                    $bin['index'],
                    bin2hex($bin['hash']),
                    bin2hex(hash('sha256', $chunk, true))
                ));
            }
            $pos += $bin['size'];
            $ret .= $chunk;
        }
        return $ret;
    }

    /**
     * Parse header of database
     *
     * @param $buffer
     * @return null|HeaderV2
     */
    protected function parseHeader($buffer)
    {
        $sig = unpack('L2',  fread($buffer, 8));
        $header = null;
        if ($sig[1] !== AbstractHeader::SIG_1) {
            throw new \RuntimeException(sprintf(
                'File signature (sig1) did not match (0X%08x != 0X%08x)',
                $sig[1],
                Headers\AbstractHeader::SIG_1
            ));
        }
        switch (true) {
            case $sig[2] === HeaderV1::SIG_2:
                // not supported now
                return null;
                break;
            case $sig[2] === HeaderV2::SIG_2:
                $header = new HeaderV2();
                $header->read($buffer);
                break;
            default:
                throw new \RuntimeException(sprintf(
                    "Second file signature did not match (0X%08x != 0X%08x or 0X%08x)",
                    $sig[2],
                    HeaderV1::SIG_2,
                    HeaderV2::SIG_2
                ));
                break;
        }

        return $header;
    }
}
