<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers\V2;

use PBergman\KeePass\Database\StreamWrapper;
use PBergman\KeePass\Headers\AbstractHeader;
use PBergman\KeePass\KeePass;

class Header extends AbstractHeader
{
    const SIG_2 = 0xB54BFB67;
    const DB_VER_DW = 0x00030000; # recent KeePass is 0x0030001,

    const COMMENT = 9;
    const CIPHER = 10;
    const COMPRESSION = 11;
    const PROTECTED_STREAM_KEY = 12;
    const START_BYTES = 13;
    const PROTECTED_STREAM = 14;

    public function __construct()
    {
        parent::__construct(15);
    }

    /**
     * set default for header
     *
     * @return array
     */
    protected function getDefaults() {
        return [
            self::VERSION => 2,
            self::ENC_TYPE =>  'rijndael',
            self::VER  => self::DB_VER_DW
        ];
    }

    /**
     * @param   resource    $fd
     * @return  $this
     *
     * @throws  HeaderException
     */
    public function read($fd)
    {
        if (!is_resource($fd)) {
            throw new \InvalidArgumentException(sprintf('Expected a resource got %s', gettext($fd)));
        }

        $ret = unpack('Lver', fread($fd, 4));
        $this[self::VER] = $ret['ver'];
        if ($this[self::VER] & 0xFFFF0000 > 0x00020000 & 0xFFFF0000) {
            HeaderException::unsupportedVersion($this[self::VER]);
        }
        $headerSize = 12; // L(4) => sig1, L(4) => sig2 & L(4) => ver
        while (true) {
            $content = fread($fd, 3);
            $ret = unpack('Ctype/Ssize', $content);
            $type = $ret['type'];
            $size = $ret['size'];
            $value = fread($fd, $size);
            if (empty($type)) {
                $this[0] = $value;
                $headerSize += $size + 3;
                break;
            }
            $headerSize += $size + 3;
            $this->validate($type, $value);
        }
        $this[self::HEADER_SIZE] = $headerSize;
        return $this;
    }

    /**
     * @param   int       $type
     * @param   string    $value
     * @return  void
     * @throws  HeaderException
     */
    protected function validate($type, $value)
    {
        switch ($type) {
            case 1:
                $this[self::COMMENT] = $value;
                break;
            case 2:
                if ($value !== "\x31\xc1\xf2\xe6\xbf\x71\x43\x50\xbe\x58\x05\x21\x6a\xfc\x5a\xff") {
                    HeaderException::cipherNotMatch();
                }
                $this[self::CIPHER] = 'eas';
                break;
            case 3:
                $ret = unpack('Vcompression', $value);
                if ($ret['compression'] > 1) {
                    HeaderException::compressionToLarge();
                }
                $this[self::COMPRESSION]= $ret['compression'];
                break;
            case 4:
                if (strlen($value) !== 32) {
                    HeaderException::invalidLength('seed random', 32);
                }
                $this[self::SEED_RAND] = $value;
                break;
            case 5:
                if (strlen($value) !== 32) {
                    HeaderException::invalidLength('seed key', 32);
                }
                $this[self::SEED_KEY] = $value;
                break;
            case 6:
                $ret = unpack('Lrounds', $value);
                $this[self::ROUNDS] = $ret['rounds'];
                break;
            case 7:
                if (strlen($value) !== 16) {
                    HeaderException::invalidLength('encryption IV', 16);
                }
                $this[self::ENC_IV] = $value;
                break;
            case 8:
                if (strlen($value) !== 32) {
                    HeaderException::invalidLength('stream key', 32);
                }
                $this[self::PROTECTED_STREAM_KEY] = $value;
                break;
            case 9:
                if (strlen($value) !== 32) {
                    HeaderException::invalidLength('start bytes', 32);
                }
                $this[self::START_BYTES] = $value;
                break;
            case 10:
                $ret = unpack('Vstream', $value);
                if ($ret['stream'] != 2) {
                    HeaderException::notSalsa20();
                }
                $this[self::PROTECTED_STREAM] = 'salsa20';
                break;
            default:
                HeaderException::unknownType($type, $value);
        }
    }

}