<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass;

use PBergman\KeePass\Crypt\Salsa20\Salsa20Interface;
use PBergman\KeePass\Crypt\Salsa20\Salsa20;
use PBergman\KeePass\Exception;
use PBergman\KeePass\Exception\ChunkException;
use PBergman\KeePass\Header\V2\Header;
use PBergman\KeePass\Key\KeyInterface;
use PBergman\KeePass\Model\XmlIndex;
use PBergman\KeePass\Node\Group;
use PBergman\KeePass\Node\Meta;
use PBergman\KeePass\Parser\Element\BinariesElementParser;
use PBergman\KeePass\Parser\Element\CustomIconsElementParser;
use PBergman\KeePass\Parser\Element\EntryElementParser;
use PBergman\KeePass\Parser\Element\GroupElementParser;
use PBergman\KeePass\Parser\Element\IndexParser;
use PBergman\KeePass\Parser\Element\MemoryProtectionElementParser;
use PBergman\KeePass\Parser\Element\MetaElementParser;
use PBergman\KeePass\Parser\Element\StringElementParser;
use PBergman\KeePass\Parser\Element\TimesElementParser;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPath;
use PBergman\KeePass\Stream\StreamInterface;
use PBergman\KeePass\Stream\TempStream;

/**
 * Class KeePass
 *
 * @package PBergman\KeePass
 */
class KeePass
{
    // file signature, should be first bytes of file
    const FILE_SIG_1 = 0x9aa2d903;
    const FILE_SIG_2 = [
        // db v1 signature
        1 => 0xb54bfb65,
        // db v2 signature
        2 => 0xb54bfb67,
    ];
    const STREAM_IV = "\xe8\x30\x09\x4b\x97\x20\x5d\x2a";
    /** @var Header  */
    protected $header;
    /** @var KeyInterface  */
    protected $key;
    /** @var StreamInterface */
    protected $tree;
    /** @var Salsa20Interface $salsa */
    protected $salsa;

    /***
     * KeePass constructor.
     *
     * @param StreamInterface $file
     * @param KeyInterface $key
     */
    public function __construct(StreamInterface $file, KeyInterface $key, Salsa20Interface $salsa = null)
    {
        $this->key = $key;
        $this->header = $this->getHeader($file);
        $this->tree = $this->parseFile($file);
        if (is_null($salsa)) {
            $salsa = new Salsa20(
                hash('sha256', $this->header->getProtectedStreamKey(), true),
                self::STREAM_IV
            );
        }
        $this->salsa = $salsa;
    }

    /**
     * @param StreamInterface $file
     * @return null|Header
     * @throws Exception\KeepassException
     * @throws Exception\SignatureException
     */
    protected function getHeader(StreamInterface $file)
    {
        $sig = unpack('L2',  $file->read(8));
        $header = null;
        if ($sig[1] !== $this::FILE_SIG_1) {
            throw new Exception\SignatureException('sig1', $sig[1], $this::FILE_SIG_1);
        }
        switch ($sig[2]) {
            case $this::FILE_SIG_2[1]:
                throw new Exception\KeepassException("keepass v1 db is not currently supported");
                break;
            case $this::FILE_SIG_2[2]:
                $header = new Header($file);
                break;
        }

        return $header;
    }

    /**
     * @param StreamInterface $file
     * @return StreamInterface
     * @throws ChunkException
     * @throws KeePassException
     */
    protected function parseFile(StreamInterface $file)
    {
        $this->key->generate($this->header);

        $content = openssl_decrypt(
            $file->readAll(),
            'AES-256-CBC',
            $this->key->getMasterKey(),
            OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,
            $this->header->getEncryptionIv()
        );

        if (substr($content, 0, 32) !== $this->header->getStartBytes()) {
            throw new \RuntimeException("corrupted or invalid database");
        }

        $buf = '';
        $pos = 32;

        while ($pos < strlen($content)) {
            $bin = unpack('Lindex/a32hash/lsize', substr($content, $pos, 40));
            $pos += 40;

            if ($bin['size'] === 0) {
                if ($bin['hash'] !== str_repeat(chr(0), 32)) {
                    throw new ChunkException(sprintf('Found mismatch for 0 chunksize, 0x32 != %s', dechex($bin['hash'])));
                }
                break;
            }

            $chunk = substr($content, $pos, $bin['size']);

            if ($bin['hash'] !== hash('sha256', $chunk, true)) {
                throw new ChunkException(sprintf(
                    'chunk hash of index %s did not match, %s != %s',
                    $bin['index'],
                    bin2hex($bin['hash']),
                    bin2hex(hash('sha256', $chunk, true))
                ));
            }

            $pos += $bin['size'];
            $buf .= $chunk;
        }

        if ($this->header->hasCompression() && false === $buf = gzdecode($buf)) {
            throw new KeePassException('could not decompress data');
        }

        $stream = new TempStream();
        $stream->write($buf);
        $stream->rewind();
        $file->close();
        return $stream;
    }

    /**
     * return a helper that hold all offsets of
     * Meta, Group en Entry elements. For the
     * Group and Entry it will also hold the
     * uuid and name that can be used for quick
     * lookup of data.
     *
     * @return XmlIndex
     */
    public function getIndex()
    {
        $indexer = new XmlIndex();
        $this->parse(new IndexParser($indexer));
        return $indexer;
    }

    /**
     * @return Meta
     */
    public function getMeta()
    {
        $meta = new Meta();
        $this->parse(
            new MetaElementParser($meta),
            new CustomIconsElementParser(),
            new BinariesElementParser(),
            new MemoryProtectionElementParser()
        );
        return $meta;
    }

    /**
     * will get a list of protected values from the xml
     *
     * @return array
     */
    public function getList()
    {
        $list = [];
        $parser = new XmlParser(new XmlPath(), new StringElementParser($list));
        while ($data = $this->tree->read(1024)) {
            $parser->parse($data, true);
        }
        $this->tree->rewind();
        return $list;
    }

    /**
     * decrypt the the given list
     *
     * @param array $list
     */
    public function unlock(array &$list)
    {
        $cypher = $this->salsa;
        foreach ($list as &$value) {
            $value = $cypher(base64_decode($value));
        }
        $cypher->reset();
    }

    /**
     * @param array|null $list
     * @param int|null $maxdepth
     * @return Group
     */
    public function getTree(array &$list = null, $maxdepth = null)
    {

        if (is_null($list)) {
            $list = [];
        }

        $collection = new Group();
        $this->parse(
            new GroupElementParser($collection, $maxdepth),
            new TimesElementParser(),
            new EntryElementParser(),
            new StringElementParser($list)
        );

        return $collection;
    }

    /**
     * @param XmlElementParserInterface[] ...$elements
     */
    protected function parse(XmlElementParserInterface ...$elements)
    {
        $parser = new XmlParser(new XmlPath(), ...$elements);
        while ($data = $this->tree->read(1024)) {
            if (XmlParserState::STATE_FINISHED === $parser->parse($data)) {
                break;
            }
        }
        $this->tree->rewind();
    }
}
