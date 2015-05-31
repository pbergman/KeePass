<?php
/**
* @author    Philip Bergman <pbergman@live.nl>
* @copyright Philip Bergman
*/
namespace PBergman\KeePass;

/**
 * Class Checksum
 *
 * @package PBergman\KeePass
 */
class Checksum
{

    /**
     * will validate stream content and update buffer with new content
     *
     * @param   Streams\AbstractStreamWrapper $buffer
     * @return  void
     * @throws \Exception
     */
    public static function unpack(Streams\AbstractStreamWrapper $buffer)
    {
        $ret = '';
        for ($pos = 0; $pos < $buffer->left();) {
            $bin = unpack('Lindex/a32hash/isize', $buffer->read(40));
            $pos += 40;
            if ($bin['size'] === 0) {
                if ($bin['hash'] !== str_repeat(chr(0), 32)) {
                    throw new KeePassException(sprintf('Found mismatch for 0 chunksize, 0x32 != %s', dechex($bin['hash'])));
                }
                break;
            }
            $chunk = $buffer->read($bin['size']);
            if ($bin['hash'] !== hash('sha256', $chunk, true)) {
                throw new KeePassException(sprintf(
                    'Chunk hash of index %s did not match, %s != %s',
                    $bin['index'],
                    bin2hex($bin['hash']),
                    bin2hex(hash('sha256', $chunk, true))
                ));
            }
            $pos += $bin['size'];
            $ret .= $chunk;
        }

        $buffer->rewrite($ret);
    }
}