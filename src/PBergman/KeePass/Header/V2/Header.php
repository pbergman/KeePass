<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Header\V2;

use PBergman\KeePass\Exception\InvalidValueException;
use PBergman\KeePass\Header\HeaderInterface;
use PBergman\KeePass\Header\HeaderTrait;
use PBergman\KeePass\Stream\StreamInterface;

/**
 * Class Header
 *
 * @package PBergman\KeePass\Header\V2
 */
class Header implements HeaderInterface
{
    use HeaderTrait;

    /** @var string  */
    protected $comment;
    /** @var string  */
    protected $cipher;
    /** @var int */
    protected $compression;
    /** @var string  */
    protected $protectedStreamKey;
    /** @var string  */
    protected $protectedStream;

    /**
     * Header constructor.
     *
     * @param StreamInterface $file
     *
     * @throws InvalidValueException
     */
    public function __construct(StreamInterface $file)
    {
        $ver = unpack('L', $file->read(4))[1];

        if ($ver & 0xffff0000 > 0x00020000 & 0xffff0000) {
            throw new InvalidValueException(sprintf('unsupported file version2 (0X%08x).', $ver));
        }

        $this->parse($file);
    }

    /**
     * @{inheritdoc}
     */
    public function __debugInfo()
    {
        return [
            'comment' => $this->comment,
            'cipher' => $this->cipher,
            'compression' => $this->compression,
            'rounds' => $this->rounds,
            'seed_rand' => \bin2hex($this->seedRandom),
            'seed_key' => \bin2hex($this->seedKey),
            'encryptionIv_iv' => \bin2hex($this->encryptionIv),
            'protected_stream_key' => \bin2hex($this->protectedStreamKey),
            'protected_stream' => \bin2hex($this->protectedStream),
            'start_byte' => \bin2hex($this->startBytes),
        ];
    }

    /**
     * @param StreamInterface $file
     *
     * @throws InvalidValueException
     */
    protected function parse(StreamInterface $file)
    {
        do {
            list($type, $value) = $this->nextValue($file);

            if (empty($type)) {
                break;
            }

            switch ($type) {
                case 1:
                   $this->comment = $value;
                   break;
                case 2:
                   if ("\x31\xc1\xf2\xe6\xbf\x71\x43\x50\xbe\x58\x05\x21\x6a\xfc\x5a\xff" !== $value) {
                       throw new InvalidValueException("cipher id did not match AES");
                   } else {
                       $this->cipher = 'eas';
                   }
                   break;
                case 3:
                   if ( 1 > ($this->compression = unpack('V', $value)[1])) {
                       throw new InvalidValueException("invalid compression value");
                   }
                   break;
                case 4:
                   if (strlen($value) !== 32) {
                       throw new InvalidValueException("invalid seed random value");
                   } else {
                       $this->seedRandom = $value;
                   }
                   break;
                case 5:
                   if (strlen($value) !== 32) {
                       throw new InvalidValueException("invalid seed key value");
                   } else {
                       $this->seedKey = $value;
                   }
                   break;
                case 6:
                   $this->rounds = unpack('V', $value)[1];
                   break;
                case 7:
                   if (strlen($value) !== 16) {
                       throw new InvalidValueException("invalid encryption iv value");
                   } else {
                       $this->encryptionIv = $value;
                   }
                   break;
                case 8:
                   if (strlen($value) !== 32) {
                       throw new InvalidValueException("invalid stream key value");
                   } else {
                       $this->protectedStreamKey = $value;
                   }
                   break;
                case 9:
                   if (strlen($value) !== 32) {
                       throw new InvalidValueException("invalid start bytes value");
                   } else {
                       $this->startBytes = $value;
                   }
                   break;
                case 10:
                   if (2 !== unpack('V', $value)[1]) {
                       throw new InvalidValueException("invalid inner stream id");
                   } else {
                       $this->protectedStream = 'salsa20';
                   }
                   break;
            }
        } while (true);
    }

    /**
     * @param StreamInterface $file
     * @return array
     */
    protected function nextValue(StreamInterface $file)
    {
        $meta = unpack('Ctype/Ssize', $file->read(3));
        $value = $file->read($meta['size']);
        return [$meta['type'], $value];
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return 2;
    }

    public function hasCompression()
    {
        return (bool)$this->compression;
    }

    /**
     * @return string
     */
    public function getProtectedStreamKey()
    {
        return $this->protectedStreamKey;
    }
}