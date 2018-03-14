<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Crypt\Salsa20;

use PBergman\KeePass\Exception\Salsa20CipherException;
use PBergman\KeePass\Stream\MemoryStream;
use PBergman\KeePass\Stream\StreamInterface;

/**
 * Class Salsa20
 *
 * base class for salsa20
 *
 * @package PBergman\KeePass
 */
abstract class AbstractSalsa20 implements Salsa20Interface
{
    /** @var int  */
    protected $rounds;
    /** @var string  */
    protected $key;
    /** @var string  */
    protected $iv;
    /** @var \SplFixedArray  */
    protected $state;
    /** @var StreamInterface  */
    protected $buf;

    /**
     * @param   string $key
     * @param   string $iv
     * @param   int $rounds
     *
     * @throws  Salsa20CipherException
     */
    public function __construct($key, $iv, $rounds = 20)
    {
        if (strlen($iv) !== 8) {
            throw Salsa20CipherException::invalidIVSize();
        }

        if (!in_array($rounds, [8, 12, 20])) {
            throw Salsa20CipherException::invalidRoundLength();
        }

        if (!in_array(strlen($key), [16, 32])) {
            throw Salsa20CipherException::invalidKeySize();
        }

        $this->rounds = $rounds;
        $this->key = $key;
        $this->iv = $iv;
        $this->buf = new MemoryStream();
        $this->reset();
    }

    public function reset()
    {
        $this->buf->truncate(0);
        $this->buf->seek(0);
        $this->initialize();
    }

    abstract protected function initialize();

    /**
     * fill the buffer that can be used for encryption/decryption
     */
    abstract protected function fillBuffer();

    /**
     * add X to end of buffer and move pointer back
     *
     * @param string $x
     */
    protected function addToBuffer($x)
    {
        $pos = $this->buf->tell();
        $this->buf->seek(0, SEEK_END);
        $this->buf->write($x);
        $this->buf->seek($pos);
    }

    /**
     * read the X bytes from cypher
     *
     * @param $size
     * @return null|string
     */
    protected function getNextBytes($size)
    {
        $free = $this->buf->left();
        if ($free < $size) {
            for ($i = 0, $c = ceil(($size - $free) / 64); $i < $c; $i++) {
                $this->fillBuffer();
            }
        }
        return $this->buf->read($size);
    }

    /**
     * @inheritdoc
     */
    public function __invoke($x)
    {
        return $x ^ $this->getNextBytes(strlen($x));
    }
}
